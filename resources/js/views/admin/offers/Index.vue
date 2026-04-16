<template>
    <Card>
        <template #title>
            <div class="flex items-center justify-between w-full">
                <span>Gestión de Ofertas</span>
                <Button
                    label="Actualizar"
                    icon="pi pi-refresh"
                    size="small"
                    outlined
                    severity="secondary"
                    :loading="isLoading"
                    @click="loadOffers"
                />
            </div>
        </template>

        <template #subtitle>Modera y gestiona todas las ofertas de empleo publicadas en la plataforma.</template>

        <template #content>
            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-4">
                <IconField class="flex-1 min-w-[200px]">
                    <InputIcon class="pi pi-search" />
                    <InputText
                        v-model="searchQuery"
                        placeholder="Buscar por título o descripción..."
                        class="w-full"
                        @keyup.enter="loadOffers"
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
                    @change="loadOffers"
                />
            </div>

            <DataTable
                :value="offers.data || []"
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

                <Column field="title" header="Título" sortable class="min-w-[250px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="12rem" height="1rem" />
                        <div v-else>
                            <span class="font-medium">{{ data.title }}</span>
                            <div class="flex gap-1 mt-1">
                                <Tag v-if="data.is_adapted" value="Adaptada" severity="info" size="small" />
                            </div>
                        </div>
                    </template>
                </Column>

                <Column header="Empresa" class="min-w-[150px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="8rem" height="1rem" />
                        <div v-else class="flex items-center gap-2">
                            <i class="pi pi-building text-gray-400" />
                            <span class="text-sm">{{ data.company?.name || data.company?.company_name || '-' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="city" header="Ciudad" sortable class="min-w-[100px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="6rem" height="1rem" />
                        <span v-else class="text-sm">{{ data.city || '-' }}</span>
                    </template>
                </Column>

                <Column header="Categoría" class="min-w-[120px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="6rem" height="1rem" />
                        <span v-else class="text-sm">{{ data.category?.name || '-' }}</span>
                    </template>
                </Column>

                <Column header="Candidaturas" class="w-[110px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="3rem" height="1.5rem" />
                        <Badge v-else :value="data.applications_count || 0" severity="info" />
                    </template>
                </Column>

                <Column field="status" header="Estado" sortable class="min-w-[120px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="5rem" height="1.5rem" />
                        <Tag
                            v-else
                            :value="offerStatusLabels[data.status] || data.status"
                            :severity="offerStatusSeverity[data.status]"
                            size="small"
                        />
                    </template>
                </Column>

                <Column field="created_at" header="Creada" sortable class="min-w-[100px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="6rem" height="1rem" />
                        <span v-else class="text-sm">{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>

                <Column header="Acciones" class="w-[200px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="8rem" height="2rem" />
                        <div v-else class="flex gap-1">
                            <Button
                                v-if="data.status !== 'PUBLISHED'"
                                v-tooltip.top="'Publicar'"
                                icon="pi pi-eye"
                                rounded text
                                severity="success"
                                size="small"
                                @click="changeStatus(data.id, 'PUBLISHED')"
                            />
                            <Button
                                v-if="data.status !== 'DRAFT'"
                                v-tooltip.top="'Pasar a borrador'"
                                icon="pi pi-eye-slash"
                                rounded text
                                severity="warn"
                                size="small"
                                @click="changeStatus(data.id, 'DRAFT')"
                            />
                            <Button
                                v-if="data.status !== 'CLOSED'"
                                v-tooltip.top="'Cerrar'"
                                icon="pi pi-lock"
                                rounded text
                                severity="secondary"
                                size="small"
                                @click="changeStatus(data.id, 'CLOSED')"
                            />
                            <Button
                                v-tooltip.top="'Eliminar'"
                                icon="pi pi-trash"
                                rounded text
                                severity="danger"
                                size="small"
                                @click="removeOffer(data.id)"
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
import useAdminOffers from '../../../composables/useAdminOffers'

const { offers, isLoading, getOffers, updateOfferStatus, deleteOffer } = useAdminOffers()

const searchQuery = ref('')
const statusFilter = ref(null)

const statusOptions = [
    { label: 'Borrador', value: 'DRAFT' },
    { label: 'Publicada', value: 'PUBLISHED' },
    { label: 'Cerrada', value: 'CLOSED' },
]

const offerStatusLabels = {
    DRAFT: 'Borrador',
    PUBLISHED: 'Publicada',
    CLOSED: 'Cerrada',
}

const offerStatusSeverity = {
    DRAFT: 'warn',
    PUBLISHED: 'success',
    CLOSED: 'secondary',
}

const loadOffers = () => {
    const params = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (statusFilter.value) params.status = statusFilter.value
    getOffers(params)
}

const changeStatus = async (offerId, status) => {
    await updateOfferStatus(offerId, status)
}

const removeOffer = async (offerId) => {
    await deleteOffer(offerId)
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
    loadOffers()
})
</script>
