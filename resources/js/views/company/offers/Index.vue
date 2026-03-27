<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mis Ofertas de Empleo</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona las ofertas publicadas por tu empresa</p>
            </div>
            <Button
                label="Nueva oferta"
                icon="pi pi-plus"
                @click="router.push({ name: 'company.offers.create' })"
                :style="{ background: '#013C7B', borderColor: '#013C7B' }"
            />
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ stats.published }}</p>
                <p class="text-xs text-gray-500 mt-1">Publicadas</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-gray-400">{{ stats.draft }}</p>
                <p class="text-xs text-gray-500 mt-1">Borradores</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-red-500">{{ stats.closed }}</p>
                <p class="text-xs text-gray-500 mt-1">Cerradas</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-center">
            <InputText v-model="searchText" placeholder="Buscar por título..." class="w-64" @keyup.enter="loadOffers" />
            <Select
                v-model="statusFilter"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Todos los estados"
                class="w-48"
                showClear
                @change="loadOffers"
            />
            <Button icon="pi pi-search" outlined @click="loadOffers" label="Buscar" />
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <DataTable
                :value="offers"
                :loading="isLoading"
                stripedRows
                class="p-datatable-sm"
                rowHover
            >
                <template #empty>
                    <div class="text-center py-12 text-gray-500">
                        <i class="pi pi-briefcase text-4xl mb-3 block opacity-30"></i>
                        <p>No tienes ofertas todavía.</p>
                        <Button
                            label="Crear tu primera oferta"
                            text
                            class="mt-2 text-[#013C7B]"
                            @click="router.push({ name: 'company.offers.create' })"
                        />
                    </div>
                </template>

                <Column field="title" header="Título" class="font-medium" />

                <Column header="Categoría">
                    <template #body="{ data }">
                        <span v-if="data.category" class="text-xs bg-blue-50 text-[#013C7B] px-2 py-1 rounded font-medium">
                            {{ data.category.name }}
                        </span>
                        <span v-else class="text-gray-400 text-xs">—</span>
                    </template>
                </Column>

                <Column field="city" header="Ciudad">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-600">{{ data.city || '—' }}</span>
                    </template>
                </Column>

                <Column header="Estado">
                    <template #body="{ data }">
                        <span :class="statusBadge(data.status)" class="text-xs font-medium px-2 py-1 rounded">
                            {{ statusLabel(data.status) }}
                        </span>
                    </template>
                </Column>

                <Column header="Fecha">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>

                <Column header="Acciones" style="width: 220px">
                    <template #body="{ data }">
                        <div class="flex gap-1">
                            <Button
                                icon="pi pi-users"
                                text
                                rounded
                                severity="secondary"
                                v-tooltip.top="'Ver candidaturas'"
                                @click="router.push({ name: 'company.offers.show', params: { id: data.id } })"
                            />
                            <Button
                                icon="pi pi-pencil"
                                text
                                rounded
                                severity="secondary"
                                v-tooltip.top="'Editar'"
                                @click="router.push({ name: 'company.offers.edit', params: { id: data.id } })"
                            />
                            <Button
                                v-if="data.status === 'DRAFT'"
                                icon="pi pi-send"
                                text
                                rounded
                                severity="success"
                                v-tooltip.top="'Publicar'"
                                @click="changeStatus(data, 'PUBLISHED')"
                            />
                            <Button
                                v-if="data.status === 'PUBLISHED'"
                                icon="pi pi-lock"
                                text
                                rounded
                                severity="warn"
                                v-tooltip.top="'Cerrar oferta'"
                                @click="changeStatus(data, 'CLOSED')"
                            />
                            <Button
                                icon="pi pi-trash"
                                text
                                rounded
                                severity="danger"
                                v-tooltip.top="'Eliminar'"
                                @click="confirmDelete(data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="flex justify-center mt-6">
            <Paginator
                :rows="pagination.per_page"
                :totalRecords="pagination.total"
                :first="(pagination.current_page - 1) * pagination.per_page"
                @page="onPageChange"
            />
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useConfirm } from 'primevue/useconfirm'
import useJobOffers from '@/composables/useJobOffers'
import { authStore } from '@/store/auth'

const router = useRouter()
const confirm = useConfirm()
const auth = authStore()
const { offers, isLoading, pagination, getOffers, updateOfferStatus, deleteOffer } = useJobOffers()

const searchText = ref('')
const statusFilter = ref(null)

const statusOptions = [
    { label: 'Borrador', value: 'DRAFT' },
    { label: 'Publicada', value: 'PUBLISHED' },
    { label: 'Cerrada', value: 'CLOSED' },
]

const stats = computed(() => ({
    total: offers.value.length,
    published: offers.value.filter(o => o.status === 'PUBLISHED').length,
    draft: offers.value.filter(o => o.status === 'DRAFT').length,
    closed: offers.value.filter(o => o.status === 'CLOSED').length,
}))

onMounted(() => loadOffers())

const loadOffers = (page = 1) => {
    const params = { company_user_id: auth.user.id, per_page: 20, page }
    if (searchText.value) params.search = searchText.value
    if (statusFilter.value) params.status = statusFilter.value
    getOffers(params)
}

const onPageChange = (e) => loadOffers(e.page + 1)

const changeStatus = async (offer, status) => {
    await updateOfferStatus(offer.id, status)
    loadOffers()
}

const confirmDelete = (offer) => {
    confirm.require({
        message: `¿Seguro que quieres eliminar "${offer.title}"? Esta acción no se puede deshacer.`,
        header: 'Eliminar oferta',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Eliminar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: async () => {
            const ok = await deleteOffer(offer.id)
            if (ok) loadOffers()
        }
    })
}

const statusLabel = (s) => ({ DRAFT: 'Borrador', PUBLISHED: 'Publicada', CLOSED: 'Cerrada' }[s] ?? s)
const statusBadge = (s) => ({
    DRAFT: 'bg-gray-100 text-gray-700',
    PUBLISHED: 'bg-green-100 text-green-700',
    CLOSED: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-700')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES') : '—'
</script>
