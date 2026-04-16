<template>
    <Card>
        <template #title>
            <div class="flex items-center justify-between w-full">
                <span>Gestión de Candidaturas</span>
                <Button
                    label="Actualizar"
                    icon="pi pi-refresh"
                    size="small"
                    outlined
                    severity="secondary"
                    :loading="isLoading"
                    @click="loadApplications"
                />
            </div>
        </template>

        <template #subtitle>Visualiza y gestiona todas las candidaturas de la plataforma.</template>

        <template #content>
            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-4">
                <IconField class="flex-1 min-w-[200px]">
                    <InputIcon class="pi pi-search" />
                    <InputText
                        v-model="searchQuery"
                        placeholder="Buscar por candidato, email u oferta..."
                        class="w-full"
                        @keyup.enter="loadApplications"
                    />
                </IconField>
                <Select
                    v-model="statusFilter"
                    :options="statusOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Estado"
                    class="w-[180px]"
                    showClear
                    @change="loadApplications"
                />
            </div>

            <DataTable
                :value="applications.data || []"
                :paginator="true"
                :rows="10"
                :rows-per-page-options="[10, 25, 50]"
                data-key="id"
                striped-rows
                size="small"
                :loading="isLoading"
            >
                <Column field="id" header="ID" sortable class="w-[60px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="3rem" height="1rem" />
                        <span v-else class="text-sm opacity-70">{{ data.id }}</span>
                    </template>
                </Column>

                <Column header="Candidato" class="min-w-[200px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="10rem" height="1rem" />
                        <div v-else>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-user text-blue-600" />
                                <span class="font-medium">{{ data.candidate?.name || '-' }}</span>
                            </div>
                            <span class="text-xs opacity-60 ml-6">{{ data.candidate?.email || '' }}</span>
                        </div>
                    </template>
                </Column>

                <Column header="Oferta" class="min-w-[250px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="12rem" height="1rem" />
                        <div v-else>
                            <span class="font-medium text-sm">{{ data.offer?.title || '-' }}</span>
                            <div class="flex items-center gap-1 mt-0.5">
                                <i class="pi pi-building text-gray-400 text-xs" />
                                <span class="text-xs opacity-60">{{ data.offer?.company?.name || '-' }}</span>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column field="status" header="Estado" sortable class="min-w-[130px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="5rem" height="1.5rem" />
                        <Tag
                            v-else
                            :value="appStatusLabels[data.status] || data.status"
                            :severity="appStatusSeverity[data.status]"
                            size="small"
                        />
                    </template>
                </Column>

                <Column field="created_at" header="Fecha" sortable class="min-w-[100px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="6rem" height="1rem" />
                        <span v-else class="text-sm">{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>

                <Column header="Acciones" class="w-[220px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="10rem" height="2rem" />
                        <div v-else class="flex gap-1">
                            <Button
                                v-if="data.status !== 'IN_REVIEW'"
                                v-tooltip.top="'En revisión'"
                                icon="pi pi-search"
                                rounded text
                                severity="info"
                                size="small"
                                @click="changeStatus(data.id, 'IN_REVIEW')"
                            />
                            <Button
                                v-if="data.status !== 'ACCEPTED'"
                                v-tooltip.top="'Aceptar'"
                                icon="pi pi-check"
                                rounded text
                                severity="success"
                                size="small"
                                @click="changeStatus(data.id, 'ACCEPTED')"
                            />
                            <Button
                                v-if="data.status !== 'REJECTED'"
                                v-tooltip.top="'Rechazar'"
                                icon="pi pi-times"
                                rounded text
                                severity="danger"
                                size="small"
                                @click="changeStatus(data.id, 'REJECTED')"
                            />
                            <Button
                                v-if="data.status !== 'CANCELED'"
                                v-tooltip.top="'Cancelar'"
                                icon="pi pi-ban"
                                rounded text
                                severity="secondary"
                                size="small"
                                @click="changeStatus(data.id, 'CANCELED')"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import useAdminApplications from '../../../composables/useAdminApplications'

const { applications, isLoading, getApplications, updateApplicationStatus } = useAdminApplications()

const searchQuery = ref('')
const statusFilter = ref(null)

const statusOptions = [
    { label: 'Enviada', value: 'SENT' },
    { label: 'En revisión', value: 'IN_REVIEW' },
    { label: 'Aceptada', value: 'ACCEPTED' },
    { label: 'Rechazada', value: 'REJECTED' },
    { label: 'Cancelada', value: 'CANCELED' },
]

const appStatusLabels = {
    SENT: 'Enviada',
    IN_REVIEW: 'En revisión',
    ACCEPTED: 'Aceptada',
    REJECTED: 'Rechazada',
    CANCELED: 'Cancelada',
}

const appStatusSeverity = {
    SENT: 'info',
    IN_REVIEW: 'warn',
    ACCEPTED: 'success',
    REJECTED: 'danger',
    CANCELED: 'secondary',
}

const loadApplications = () => {
    const params = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (statusFilter.value) params.status = statusFilter.value
    getApplications(params)
}

const changeStatus = async (applicationId, status) => {
    await updateApplicationStatus(applicationId, status)
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

onMounted(() => {
    loadApplications()
})
</script>
