<template>
    <div class="grid grid-cols-1 gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mis Candidaturas</h1>
                <p class="text-sm text-gray-500 mt-1">Seguimiento de todas tus solicitudes de empleo</p>
            </div>
            <router-link to="/ofertas">
                <Button label="Buscar ofertas" icon="pi pi-search" size="small"
                    style="background-color:#013C7B;border-color:#013C7B;" />
            </router-link>
        </div>

        <!-- Stats rápidas -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div v-for="s in statCards" :key="s.status"
                class="bg-white dark:bg-surface-900 border rounded-xl p-4 text-center cursor-pointer transition-all"
                :class="activeFilter === s.status ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-surface-700 hover:border-gray-300'"
                @click="activeFilter = activeFilter === s.status ? null : s.status">
                <p class="text-2xl font-bold" :class="s.color">{{ s.count }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ s.label }}</p>
            </div>
        </div>

        <!-- Lista -->
        <div v-if="isLoading" class="flex flex-col gap-3">
            <div v-for="n in 4" :key="n" class="bg-white dark:bg-surface-900 rounded-xl border border-gray-200 dark:border-surface-700 p-5">
                <Skeleton height="1.25rem" class="mb-2" width="60%" />
                <Skeleton height="1rem" width="40%" class="mb-3" />
                <Skeleton height="2rem" width="8rem" />
            </div>
        </div>

        <div v-else-if="filteredApplications.length" class="flex flex-col gap-3">
            <div v-for="app in filteredApplications" :key="app.id"
                class="bg-white dark:bg-surface-900 rounded-xl border border-gray-200 dark:border-surface-700 p-5 hover:shadow-sm transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Info oferta -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-surface-800 flex items-center justify-center shrink-0">
                            <i class="pi pi-briefcase text-gray-500"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">
                                {{ app.offer?.title ?? 'Oferta desconocida' }}
                            </h3>
                            <p class="text-sm text-[#013C7B] font-medium">
                                {{ app.offer?.company?.name ?? '' }}
                            </p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span v-if="app.offer?.city"
                                    class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="pi pi-map-marker"></i> {{ app.offer.city }}
                                </span>
                                <span v-if="app.offer?.modality?.name"
                                    class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="pi pi-desktop"></i> {{ app.offer.modality.name }}
                                </span>
                                <span v-if="app.offer?.contract_type?.name"
                                    class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="pi pi-file-edit"></i> {{ app.offer.contract_type.name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Estado + acciones -->
                    <div class="flex flex-col items-start md:items-end gap-2 shrink-0">
                        <Tag :value="appStatusLabel(app.status)" :severity="appStatusSeverity(app.status)" />
                        <p class="text-xs text-gray-400">Solicitada el {{ formatDate(app.created_at) }}</p>

                        <div class="flex gap-2 mt-1">
                            <router-link :to="{ name: 'ofertas.show', params: { id: app.offer_id ?? app.offer?.id } }">
                                <Button label="Ver oferta" outlined size="small" severity="secondary" icon="pi pi-eye" />
                            </router-link>
                            <Button v-if="app.status !== 'CANCELED' && app.status !== 'REJECTED' && app.status !== 'ACCEPTED'"
                                label="Cancelar" outlined severity="danger" size="small"
                                :loading="cancelingId === app.id"
                                @click="handleCancel(app.id)" />
                        </div>
                    </div>
                </div>

                <!-- Nota de la empresa (si hay) -->
                <div v-if="app.company_notes"
                    class="mt-4 pt-4 border-t border-gray-100 dark:border-surface-700">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nota de la empresa</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 italic">{{ app.company_notes }}</p>
                </div>

                <!-- Timeline de estado -->
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-surface-700 flex items-center gap-0">
                    <template v-for="(step, idx) in statusSteps" :key="step.value">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                :class="isStepCompleted(app.status, step.value)
                                    ? 'bg-[#013C7B] text-white'
                                    : 'bg-gray-200 dark:bg-surface-700 text-gray-400'">
                                <i :class="isStepCompleted(app.status, step.value) ? 'pi pi-check' : 'pi pi-circle'" class="text-xs"></i>
                            </div>
                            <p class="text-xs mt-1 w-16 leading-tight"
                                :class="isStepCompleted(app.status, step.value) ? 'text-[#013C7B] font-medium' : 'text-gray-400'">
                                {{ step.label }}
                            </p>
                        </div>
                        <div v-if="idx < statusSteps.length - 1" class="flex-1 h-0.5 mb-4 transition-all"
                            :class="isStepCompleted(app.status, statusSteps[idx + 1]?.value)
                                ? 'bg-[#013C7B]'
                                : 'bg-gray-200 dark:bg-surface-700'" />
                    </template>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-16">
            <i class="pi pi-send text-5xl text-gray-300 mb-4 block"></i>
            <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
                {{ activeFilter ? 'No tienes candidaturas con este estado' : 'Todavía no has solicitado ninguna oferta' }}
            </p>
            <router-link to="/ofertas" class="inline-block mt-4">
                <Button label="Explorar ofertas de empleo" icon="pi pi-search"
                    style="background-color:#013C7B;border-color:#013C7B;" />
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import useApplications from '@/composables/useApplications'
import { authStore } from '@/store/auth'

const auth = authStore()
const { applications, isLoading, getMyApplications, cancelApplication } = useApplications()
const activeFilter = ref(null)
const cancelingId = ref(null)

const appStatusOptions = [
    { label: 'Enviada',     value: 'SENT',      severity: 'info' },
    { label: 'En revisión', value: 'IN_REVIEW',  severity: 'warn' },
    { label: 'Aceptada',    value: 'ACCEPTED',   severity: 'success' },
    { label: 'Rechazada',   value: 'REJECTED',   severity: 'danger' },
    { label: 'Cancelada',   value: 'CANCELED',   severity: 'secondary' },
]

const statusSteps = [
    { label: 'Enviada',     value: 'SENT' },
    { label: 'En revisión', value: 'IN_REVIEW' },
    { label: 'Aceptada',    value: 'ACCEPTED' },
]

const stepOrder = ['SENT', 'IN_REVIEW', 'ACCEPTED']

const isStepCompleted = (currentStatus, stepValue) => {
    if (currentStatus === 'REJECTED' || currentStatus === 'CANCELED') return false
    const currentIdx = stepOrder.indexOf(currentStatus)
    const stepIdx = stepOrder.indexOf(stepValue)
    return stepIdx <= currentIdx
}

const appStatusLabel = (s) => appStatusOptions.find(o => o.value === s)?.label ?? s
const appStatusSeverity = (s) => appStatusOptions.find(o => o.value === s)?.severity ?? 'secondary'
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const statCards = computed(() => [
    { label: 'Total',       status: null,        count: applications.value.length,                                                          color: 'text-gray-900 dark:text-white' },
    { label: 'Enviadas',    status: 'SENT',       count: applications.value.filter(a => a.status === 'SENT').length,                        color: 'text-blue-600' },
    { label: 'En revisión', status: 'IN_REVIEW',  count: applications.value.filter(a => a.status === 'IN_REVIEW').length,                   color: 'text-yellow-600' },
    { label: 'Aceptadas',   status: 'ACCEPTED',   count: applications.value.filter(a => a.status === 'ACCEPTED').length,                    color: 'text-green-600' },
    { label: 'Rechazadas',  status: 'REJECTED',   count: applications.value.filter(a => a.status === 'REJECTED' || a.status === 'CANCELED').length, color: 'text-red-500' },
])

const filteredApplications = computed(() => {
    if (!activeFilter.value) return applications.value
    if (activeFilter.value === 'REJECTED') return applications.value.filter(a => a.status === 'REJECTED' || a.status === 'CANCELED')
    return applications.value.filter(a => a.status === activeFilter.value)
})

const handleCancel = async (id) => {
    cancelingId.value = id
    await cancelApplication(id)
    cancelingId.value = null
}

onMounted(() => getMyApplications(auth.user.id))
</script>
