<template>
    <Card>
        <template #title>
            <div class="flex items-center justify-between w-full">
                <span>Gestión de Empresas</span>
                <Button
                    label="Actualizar"
                    icon="pi pi-refresh"
                    size="small"
                    outlined
                    severity="secondary"
                    :loading="isLoading"
                    @click="loadCompanies"
                />
            </div>
        </template>

        <template #subtitle>Administra las empresas registradas. Valida, rechaza o bloquea sus perfiles.</template>

        <template #content>
            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-4">
                <IconField class="flex-1 min-w-[200px]">
                    <InputIcon class="pi pi-search" />
                    <InputText
                        v-model="searchQuery"
                        placeholder="Buscar por nombre, sector o ciudad..."
                        class="w-full"
                        @keyup.enter="loadCompanies"
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
                    @change="loadCompanies"
                />
            </div>

            <DataTable
                :value="companies.data || []"
                :paginator="true"
                :rows="10"
                :rows-per-page-options="[10, 25, 50]"
                data-key="user_id"
                striped-rows
                size="small"
                :loading="isLoading"
            >
                <Column field="company_name" header="Empresa" sortable class="min-w-[200px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="10rem" height="1rem" />
                        <div v-else class="flex items-center gap-2">
                            <i class="pi pi-building text-blue-600" />
                            <span class="font-medium">{{ data.company_name || '-' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="sector" header="Sector" sortable class="min-w-[150px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="8rem" height="1rem" />
                        <span v-else>{{ data.sector || '-' }}</span>
                    </template>
                </Column>

                <Column field="city" header="Ciudad" sortable class="min-w-[120px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="6rem" height="1rem" />
                        <span v-else>{{ data.city || '-' }}</span>
                    </template>
                </Column>

                <Column field="user" header="Email" class="min-w-[200px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="10rem" height="1rem" />
                        <span v-else class="text-sm">{{ data.user?.email || '-' }}</span>
                    </template>
                </Column>

                <Column header="Adaptaciones" class="min-w-[120px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="4rem" height="1.5rem" />
                        <div v-else class="flex flex-wrap gap-1">
                            <Tag v-if="data.offers_adapted_positions" value="Adaptadas" severity="info" size="small" />
                            <Tag v-if="data.offers_remote_work" value="Remoto" severity="secondary" size="small" />
                            <span v-if="!data.offers_adapted_positions && !data.offers_remote_work" class="text-sm opacity-50">-</span>
                        </div>
                    </template>
                </Column>

                <Column field="validation_status" header="Estado" sortable class="min-w-[130px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="5rem" height="1.5rem" />
                        <Tag
                            v-else
                            :value="statusLabels[data.validation_status] || data.validation_status"
                            :severity="statusSeverity[data.validation_status]"
                            size="small"
                        />
                    </template>
                </Column>

                <Column header="Acciones" class="w-[200px]">
                    <template #body="{ data }">
                        <Skeleton v-if="isLoading" width="8rem" height="2rem" />
                        <div v-else class="flex gap-1">
                            <Button
                                v-if="data.validation_status !== 'VALIDATED'"
                                v-tooltip.top="'Validar'"
                                icon="pi pi-check"
                                rounded text
                                severity="success"
                                size="small"
                                @click="changeStatus(data.user_id, 'VALIDATED')"
                            />
                            <Button
                                v-if="data.validation_status !== 'REJECTED'"
                                v-tooltip.top="'Rechazar'"
                                icon="pi pi-times"
                                rounded text
                                severity="warn"
                                size="small"
                                @click="changeStatus(data.user_id, 'REJECTED')"
                            />
                            <Button
                                v-if="data.validation_status !== 'BLOCKED'"
                                v-tooltip.top="'Bloquear'"
                                icon="pi pi-ban"
                                rounded text
                                severity="danger"
                                size="small"
                                @click="changeStatus(data.user_id, 'BLOCKED')"
                            />
                            <Button
                                v-if="data.validation_status === 'BLOCKED' || data.validation_status === 'REJECTED'"
                                v-tooltip.top="'Restaurar a Pendiente'"
                                icon="pi pi-replay"
                                rounded text
                                severity="secondary"
                                size="small"
                                @click="changeStatus(data.user_id, 'PENDING')"
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
import useAdminCompanies from '../../../composables/useAdminCompanies'

const { companies, isLoading, getCompanies, updateCompanyStatus } = useAdminCompanies()

const searchQuery = ref('')
const statusFilter = ref(null)

const statusOptions = [
    { label: 'Pendiente', value: 'PENDING' },
    { label: 'Validada', value: 'VALIDATED' },
    { label: 'Rechazada', value: 'REJECTED' },
    { label: 'Bloqueada', value: 'BLOCKED' },
]

const statusLabels = {
    PENDING: 'Pendiente',
    VALIDATED: 'Validada',
    REJECTED: 'Rechazada',
    BLOCKED: 'Bloqueada',
}

const statusSeverity = {
    PENDING: 'warn',
    VALIDATED: 'success',
    REJECTED: 'danger',
    BLOCKED: 'danger',
}

const loadCompanies = () => {
    const params = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (statusFilter.value) params.validation_status = statusFilter.value
    getCompanies(params)
}

const changeStatus = async (userId, status) => {
    await updateCompanyStatus(userId, status)
}

onMounted(() => {
    loadCompanies()
})
</script>
