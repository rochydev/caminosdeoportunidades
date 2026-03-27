<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="container mx-auto max-w-7xl">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Buscar Ofertas de Empleo</h1>
                <p class="text-gray-500 mt-1">Encuentra oportunidades adaptadas a tu perfil</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Filters Panel -->
                <aside class="lg:w-72 flex-shrink-0">
                    <Card class="rounded-xl border border-gray-200 shadow-sm sticky top-4">
                        <template #title>
                            <span class="text-base font-bold text-gray-900">Filtros</span>
                        </template>
                        <template #content>
                            <div class="flex flex-col gap-4">

                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Buscar</label>
                                    <InputText
                                        v-model="filters.search"
                                        placeholder="Título o descripción..."
                                        class="w-full"
                                        @keyup.enter="applyFilters"
                                    />
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Ciudad</label>
                                    <InputText
                                        v-model="filters.city"
                                        placeholder="Ej: Madrid"
                                        class="w-full"
                                        @keyup.enter="applyFilters"
                                    />
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Categoría</label>
                                    <Select
                                        v-model="filters.category_id"
                                        :options="catalogs.categories"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Todas"
                                        class="w-full"
                                        showClear
                                    />
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Tipo de contrato</label>
                                    <Select
                                        v-model="filters.contract_type_id"
                                        :options="catalogs.contractTypes"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Todos"
                                        class="w-full"
                                        showClear
                                    />
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Modalidad</label>
                                    <Select
                                        v-model="filters.modality_id"
                                        :options="catalogs.modalityTypes"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Todas"
                                        class="w-full"
                                        showClear
                                    />
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <Checkbox v-model="filters.is_adapted" :binary="true" inputId="adapted" />
                                    <label for="adapted" class="text-sm text-gray-700 cursor-pointer">Solo puestos adaptados</label>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <Button
                                        label="Buscar"
                                        icon="pi pi-search"
                                        class="flex-1"
                                        @click="applyFilters"
                                        :style="{ background: '#013C7B', borderColor: '#013C7B' }"
                                    />
                                    <Button
                                        icon="pi pi-times"
                                        severity="secondary"
                                        outlined
                                        @click="clearFilters"
                                        v-tooltip.top="'Limpiar filtros'"
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>
                </aside>

                <!-- Results -->
                <div class="flex-1">
                    <!-- Results count -->
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-500">
                            {{ pagination.total }} oferta{{ pagination.total !== 1 ? 's' : '' }} encontrada{{ pagination.total !== 1 ? 's' : '' }}
                        </p>
                    </div>

                    <!-- Loading -->
                    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <Skeleton v-for="i in 6" :key="i" height="220px" class="rounded-xl" />
                    </div>

                    <!-- Empty -->
                    <div v-else-if="offers.length === 0" class="text-center py-16 text-gray-500">
                        <i class="pi pi-search text-4xl mb-4 block opacity-30"></i>
                        <p class="font-medium">No se encontraron ofertas</p>
                        <p class="text-sm mt-1">Prueba con otros filtros</p>
                    </div>

                    <!-- Grid -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div
                            v-for="job in offers"
                            :key="job.id"
                            class="rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow bg-white flex flex-col cursor-pointer"
                            @click="goToOffer(job.id)"
                        >
                            <!-- Company banner -->
                            <div class="h-28 overflow-hidden bg-gray-100">
                                <img
                                    v-if="job?.company?.avatar"
                                    :src="job.company.avatar"
                                    :alt="job.company.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                                    <i class="pi pi-building text-[#013C7B] text-3xl opacity-40"></i>
                                </div>
                            </div>

                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-bold text-gray-900 line-clamp-2 flex-1">{{ job.title }}</h3>
                                    <span v-if="job.is_adapted" class="shrink-0 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-medium">
                                        Adaptado
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">{{ job.company?.name }}</p>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span v-if="job.category?.name" class="text-xs bg-blue-50 text-[#013C7B] px-2 py-1 rounded font-medium">
                                        {{ job.category.name }}
                                    </span>
                                    <span v-if="job.city" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                        <i class="pi pi-map-marker text-xs mr-1"></i>{{ job.city }}
                                    </span>
                                    <span v-if="job.modality?.name" class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded">
                                        {{ job.modality.name }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 line-clamp-2 mb-4 flex-1">{{ job.description }}</p>

                                <div class="mt-auto pt-3 border-t border-gray-100">
                                    <Button
                                        v-if="!appliedIds.has(job.id)"
                                        label="Solicitar"
                                        class="w-full"
                                        :loading="applyingId === job.id"
                                        @click.stop="handleApply(job)"
                                        :style="{ background: '#013C7B', borderColor: '#013C7B' }"
                                    />
                                    <div v-else class="text-sm text-green-600 text-center py-1 font-medium flex items-center justify-center gap-2">
                                        <i class="pi pi-check-circle"></i> Candidatura enviada
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="pagination.last_page > 1" class="flex justify-center mt-8">
                        <Paginator
                            :rows="pagination.per_page"
                            :totalRecords="pagination.total"
                            :first="(pagination.current_page - 1) * pagination.per_page"
                            @page="onPageChange"
                            :rowsPerPageOptions="[15, 30, 50]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import useJobOffers from '@/composables/useJobOffers'
import useJobApplications from '@/composables/jobApplications'
import { authStore } from '@/store/auth'

const router = useRouter()
const auth = authStore()
const { offers, catalogs, isLoading, pagination, getCatalogs, getOffers } = useJobOffers()
const { applyToOffer, isApplying } = useJobApplications()

const appliedIds = ref(new Set())
const applyingId = ref(null)

const filters = reactive({
    search: '',
    city: '',
    category_id: null,
    contract_type_id: null,
    modality_id: null,
    is_adapted: false,
})

onMounted(async () => {
    await getCatalogs()
    await loadOffers()
})

const buildParams = (page = 1) => {
    const params = { status: 'PUBLISHED', per_page: 15, page }
    if (filters.search) params.search = filters.search
    if (filters.city) params.city = filters.city
    if (filters.category_id) params.category_id = filters.category_id
    if (filters.contract_type_id) params.contract_type_id = filters.contract_type_id
    if (filters.modality_id) params.modality_id = filters.modality_id
    if (filters.is_adapted) params.is_adapted = true
    return params
}

const loadOffers = (page = 1) => getOffers(buildParams(page))

const applyFilters = () => loadOffers(1)

const clearFilters = () => {
    Object.assign(filters, { search: '', city: '', category_id: null, contract_type_id: null, modality_id: null, is_adapted: false })
    loadOffers(1)
}

const onPageChange = (event) => loadOffers(event.page + 1)

const goToOffer = (id) => router.push({ name: 'jobs.show', params: { id } })

const handleApply = async (job) => {
    applyingId.value = job.id
    try {
        await applyToOffer(job.id, auth.user.id)
        appliedIds.value.add(job.id)
    } catch {
        // error handled in composable
    } finally {
        applyingId.value = null
    }
}
</script>
