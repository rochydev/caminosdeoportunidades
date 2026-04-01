<template>
    <div>
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Candidaturas Recibidas</span>
                    <Button label="Actualizar" icon="pi pi-refresh" size="small" outlined severity="secondary"
                        :loading="isLoadingApps" @click="loadAll" />
                </div>
            </template>
            <template #subtitle>Revisa y gestiona todas las solicitudes recibidas para tus ofertas.</template>

            <template #content>
                <!-- Filtros -->
                <div class="flex gap-3 mb-4 flex-wrap items-end">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-gray-500">Filtrar por oferta</label>
                        <Select v-model="filterOfferId" :options="[{ id: null, title: 'Todas las ofertas' }, ...(offers.data ?? [])]"
                            option-label="title" option-value="id" class="w-56" @change="applyFilter" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-gray-500">Filtrar por estado</label>
                        <Select v-model="filterAppStatus" :options="[{ label: 'Todos', value: null }, ...appStatusOptions]"
                            option-label="label" option-value="value" class="w-44" @change="applyFilter" />
                    </div>
                </div>

                <DataTable
                    :value="filteredApplications"
                    :loading="isLoadingApps"
                    data-key="id"
                    striped-rows
                    size="small"
                    :paginator="true"
                    :rows="15"
                    :rows-per-page-options="[15, 30, 50]"
                    sort-field="created_at"
                    :sort-order="-1"
                >
                    <template #empty>
                        <div class="text-center py-8 text-gray-400">
                            <i class="pi pi-users text-4xl mb-3 block"></i>
                            <p>No hay candidaturas para tus ofertas todavía.</p>
                        </div>
                    </template>

                    <Column header="Candidato" class="min-w-[180px]">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm shrink-0">
                                    {{ candidateName(data).charAt(0).toUpperCase() }}
                                </div>
                                <span class="font-medium text-sm">{{ candidateName(data) }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Oferta" class="min-w-[200px]">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-700">{{ data.offer?.title ?? '-' }}</span>
                        </template>
                    </Column>

                    <Column header="Estado" class="w-[140px]" sortable sort-field="status">
                        <template #body="{ data }">
                            <Tag :value="appStatusLabel(data.status)" :severity="appStatusSeverity(data.status)"
                                size="small" />
                        </template>
                    </Column>

                    <Column header="Fecha" class="w-[120px]" sortable sort-field="created_at">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</span>
                        </template>
                    </Column>

                    <Column header="Acciones" class="w-[100px]">
                        <template #body="{ data }">
                            <Button v-tooltip.top="'Ver / Gestionar'" icon="pi pi-eye" rounded text severity="primary"
                                size="small" @click="openDetail(data)" />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Dialog Detalle / Gestión -->
        <Dialog v-model:visible="detailVisible" header="Gestionar candidatura" :style="{ width: '560px' }" :modal="true">
            <div v-if="selectedApp" class="flex flex-col gap-4 pt-2">
                <!-- Info candidato -->
                <div class="bg-gray-50 dark:bg-surface-800 rounded-lg p-4 flex flex-col gap-1">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ candidateName(selectedApp) }}</p>
                    <p class="text-sm text-gray-500">{{ selectedApp.candidate?.email ?? '-' }}</p>
                </div>

                <!-- Info oferta -->
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Oferta</label>
                    <p class="text-sm font-medium">{{ selectedApp.offer?.title ?? '-' }}</p>
                </div>

                <!-- Estado actual -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Estado de la candidatura</label>
                    <Select v-model="editStatus" :options="appStatusOptions" option-label="label" option-value="value"
                        class="w-full" />
                </div>

                <!-- Notas empresa -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Notas internas</label>
                    <Textarea v-model="editNotes" rows="3"
                        placeholder="Notas privadas sobre esta candidatura (no visibles al candidato)..."
                        class="w-full" />
                </div>

                <!-- Fecha solicitud -->
                <p class="text-xs text-gray-400">Solicitud recibida el {{ formatDate(selectedApp.created_at) }}</p>
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="detailVisible = false" />
                <Button label="Guardar cambios" icon="pi pi-check" :loading="isLoadingApps"
                    @click="handleUpdateApplication" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import useJobOffers from '@/composables/useJobOffers'
import useJobApplications from '@/composables/useJobApplications'
import { authStore } from '@/store/auth'

const auth = authStore()
const { offers, getOffers } = useJobOffers()
const { applications, isLoading: isLoadingApps, getApplicationsForOffers, updateApplication } = useJobApplications()

const filterOfferId = ref(null)
const filterAppStatus = ref(null)
const detailVisible = ref(false)
const selectedApp = ref(null)
const editStatus = ref(null)
const editNotes = ref('')

const appStatusOptions = [
    { label: 'Enviada', value: 'SENT', severity: 'info' },
    { label: 'En revisión', value: 'IN_REVIEW', severity: 'warn' },
    { label: 'Aceptada', value: 'ACCEPTED', severity: 'success' },
    { label: 'Rechazada', value: 'REJECTED', severity: 'danger' },
    { label: 'Cancelada', value: 'CANCELED', severity: 'secondary' },
]

const appStatusLabel = (s) => appStatusOptions.find(o => o.value === s)?.label ?? s
const appStatusSeverity = (s) => appStatusOptions.find(o => o.value === s)?.severity ?? 'secondary'

const candidateName = (app) => {
    const c = app.candidate
    if (!c) return 'Candidato desconocido'
    return [c.name, c.surname1].filter(Boolean).join(' ') || c.email || 'Sin nombre'
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const filteredApplications = computed(() => {
    let list = applications.value
    if (filterOfferId.value) list = list.filter(a => a.offer_id === filterOfferId.value)
    if (filterAppStatus.value) list = list.filter(a => a.status === filterAppStatus.value)
    return list
})

const loadAll = async () => {
    await getOffers({ company_user_id: auth.user.id })
    const offerIds = (offers.value?.data ?? []).map(o => o.id)
    await getApplicationsForOffers(offerIds)
}

const applyFilter = () => { /* filteredApplications computed handles it */ }

const openDetail = (app) => {
    selectedApp.value = app
    editStatus.value = app.status
    editNotes.value = app.company_notes ?? ''
    detailVisible.value = true
}

const handleUpdateApplication = async () => {
    const result = await updateApplication(selectedApp.value.id, {
        status: editStatus.value,
        company_notes: editNotes.value
    })
    if (result) {
        // Actualizar localmente
        const idx = applications.value.findIndex(a => a.id === selectedApp.value.id)
        if (idx !== -1) {
            applications.value[idx] = { ...applications.value[idx], status: editStatus.value, company_notes: editNotes.value }
        }
        detailVisible.value = false
    }
}

onMounted(loadAll)
</script>
