<template>
    <div>
        <!-- Header -->
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Mis Ofertas de Empleo</span>
                    <div class="flex gap-2">
                        <Button label="Actualizar" icon="pi pi-refresh" size="small" outlined severity="secondary"
                            :loading="isLoading" @click="loadOffers" />
                        <Button label="Nueva Oferta" icon="pi pi-plus" size="small" severity="primary"
                            @click="openCreate" />
                    </div>
                </div>
            </template>
            <template #subtitle>Gestiona las ofertas de empleo publicadas por tu empresa.</template>

            <template #content>
                <!-- Filtro de estado -->
                <div class="flex gap-2 mb-4 flex-wrap">
                    <Button
                        v-for="s in statusOptions"
                        :key="s.value"
                        :label="s.label"
                        size="small"
                        :outlined="filterStatus !== s.value"
                        :severity="s.severity"
                        @click="filterStatus = filterStatus === s.value ? null : s.value"
                    />
                </div>

                <DataTable
                    :value="filteredOffers"
                    :loading="isLoading"
                    data-key="id"
                    striped-rows
                    size="small"
                    :paginator="true"
                    :rows="10"
                    :rows-per-page-options="[10, 25, 50]"
                    sort-field="created_at"
                    :sort-order="-1"
                >
                    <template #empty>
                        <div class="text-center py-8 text-gray-400">
                            <i class="pi pi-briefcase text-4xl mb-3 block"></i>
                            <p>No tienes ofertas publicadas todavía.</p>
                            <Button label="Crear primera oferta" icon="pi pi-plus" class="mt-3" size="small"
                                @click="openCreate" />
                        </div>
                    </template>

                    <Column field="title" header="Título" sortable class="min-w-[200px]">
                        <template #body="{ data }">
                            <span class="font-medium">{{ data.title }}</span>
                        </template>
                    </Column>

                    <Column field="category" header="Categoría" class="min-w-[160px]">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-600">{{ data.category?.name ?? '-' }}</span>
                        </template>
                    </Column>

                    <Column field="city" header="Ciudad" class="min-w-[120px]">
                        <template #body="{ data }">
                            <span class="text-sm">{{ data.city ?? '-' }}</span>
                        </template>
                    </Column>

                    <Column field="contract_type" header="Contrato" class="min-w-[130px]">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-600">{{ data.contract_type?.name ?? '-' }}</span>
                        </template>
                    </Column>

                    <Column field="status" header="Estado" sortable class="w-[120px]">
                        <template #body="{ data }">
                            <Tag :value="statusLabel(data.status)" :severity="statusSeverity(data.status)" size="small" />
                        </template>
                    </Column>

                    <Column field="created_at" header="Fecha" sortable class="w-[120px]">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</span>
                        </template>
                    </Column>

                    <Column header="Acciones" class="w-[110px]">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Button v-tooltip.top="'Editar'" icon="pi pi-pencil" rounded text severity="secondary"
                                    size="small" @click="openEdit(data)" />
                                <Button v-tooltip.top="'Eliminar'" icon="pi pi-trash" rounded text severity="danger"
                                    size="small" @click="handleDelete(data.id)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Dialog Crear/Editar -->
        <Dialog v-model:visible="dialogVisible" :header="editMode ? 'Editar Oferta' : 'Nueva Oferta'"
            :style="{ width: '720px' }" :modal="true" :closable="true">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- Título -->
                <div class="col-span-2 flex flex-col gap-1">
                    <label class="font-medium text-sm">Título <span class="text-red-500">*</span></label>
                    <InputText v-model="offer.title" placeholder="Ej: Desarrollador/a Web" class="w-full" />
                </div>

                <!-- Descripción -->
                <div class="col-span-2 flex flex-col gap-1">
                    <label class="font-medium text-sm">Descripción <span class="text-red-500">*</span></label>
                    <Textarea v-model="offer.description" rows="4" placeholder="Describe el puesto..." class="w-full" />
                </div>

                <!-- Requisitos -->
                <div class="col-span-2 flex flex-col gap-1">
                    <label class="font-medium text-sm">Requisitos</label>
                    <Textarea v-model="offer.requirements" rows="3" placeholder="Requisitos del puesto..." class="w-full" />
                </div>

                <!-- Adaptaciones -->
                <div class="col-span-2 flex flex-col gap-1">
                    <label class="font-medium text-sm">Adaptaciones</label>
                    <Textarea v-model="offer.adaptations" rows="2"
                        placeholder="Adaptaciones disponibles para el puesto..." class="w-full" />
                </div>

                <!-- Ciudad -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Ciudad</label>
                    <InputText v-model="offer.city" placeholder="Ej: Madrid" class="w-full" />
                </div>

                <!-- Estado -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Estado</label>
                    <Select v-model="offer.status" :options="statusOptions" option-label="label" option-value="value"
                        class="w-full" />
                </div>

                <!-- Categoría -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Categoría</label>
                    <Select v-model="offer.category_id" :options="jobCategories" option-label="name" option-value="id"
                        placeholder="Seleccionar..." :show-clear="true" class="w-full" />
                </div>

                <!-- Tipo de contrato -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Tipo de contrato</label>
                    <Select v-model="offer.contract_type_id" :options="contractTypes" option-label="name"
                        option-value="id" placeholder="Seleccionar..." :show-clear="true" class="w-full" />
                </div>

                <!-- Jornada -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Jornada</label>
                    <Select v-model="offer.workday_type_id" :options="workdayTypes" option-label="name"
                        option-value="id" placeholder="Seleccionar..." :show-clear="true" class="w-full" />
                </div>

                <!-- Modalidad -->
                <div class="flex flex-col gap-1">
                    <label class="font-medium text-sm">Modalidad</label>
                    <Select v-model="offer.modality_id" :options="modalityTypes" option-label="name" option-value="id"
                        placeholder="Seleccionar..." :show-clear="true" class="w-full" />
                </div>

                <!-- Puesto adaptado -->
                <div class="col-span-2 flex items-center gap-2">
                    <Checkbox v-model="offer.is_adapted" inputId="is_adapted" binary />
                    <label for="is_adapted" class="text-sm cursor-pointer">
                        Puesto adaptado para personas con discapacidad
                    </label>
                </div>
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="dialogVisible = false" />
                <Button :label="editMode ? 'Guardar cambios' : 'Crear oferta'" icon="pi pi-check"
                    :loading="isLoading" @click="handleSave" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import useJobOffers from '@/composables/useJobOffers'
import { authStore } from '@/store/auth'

const auth = authStore()
const { offers, offer, isLoading, resetOffer, setOffer, getOffers, createOffer, updateOffer, deleteOffer } = useJobOffers()

const dialogVisible = ref(false)
const editMode = ref(false)
const filterStatus = ref(null)

const jobCategories = ref([])
const contractTypes = ref([])
const workdayTypes = ref([])
const modalityTypes = ref([])

const statusOptions = [
    { label: 'Borrador', value: 'DRAFT', severity: 'secondary' },
    { label: 'Publicada', value: 'PUBLISHED', severity: 'success' },
    { label: 'Cerrada', value: 'CLOSED', severity: 'danger' },
]

const statusLabel = (s) => statusOptions.find(o => o.value === s)?.label ?? s
const statusSeverity = (s) => statusOptions.find(o => o.value === s)?.severity ?? 'secondary'

const filteredOffers = computed(() => {
    const list = offers.value?.data ?? []
    if (!filterStatus.value) return list
    return list.filter(o => o.status === filterStatus.value)
})

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const loadOffers = () => getOffers({ company_user_id: auth.user.id })

const loadLookups = async () => {
    const [cats, contracts, workdays, modalities] = await Promise.all([
        axios.get('/api/job-categories').then(r => r.data),
        axios.get('/api/contract-types').then(r => r.data),
        axios.get('/api/workday-types').then(r => r.data),
        axios.get('/api/modality-types').then(r => r.data),
    ])
    jobCategories.value = cats
    contractTypes.value = contracts
    workdayTypes.value = workdays
    modalityTypes.value = modalities
}

const openCreate = () => {
    resetOffer()
    offer.value.company_user_id = auth.user.id
    offer.value.status = 'DRAFT'
    editMode.value = false
    dialogVisible.value = true
}

const openEdit = (data) => {
    setOffer(data)
    editMode.value = true
    dialogVisible.value = true
}

const handleSave = async () => {
    const result = editMode.value ? await updateOffer() : await createOffer()
    if (result) {
        dialogVisible.value = false
        loadOffers()
    }
}

const handleDelete = async (id) => {
    const deleted = await deleteOffer(id)
    if (deleted) loadOffers()
}

onMounted(() => {
    loadOffers()
    loadLookups()
})
</script>
