<template>
    <div class="bg-[#FAFAFA] min-h-screen">

        <!-- Hero buscador -->
        <div class="bg-white border-b border-gray-200 py-8">
            <div class="container mx-auto px-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Ofertas de empleo</h1>
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <InputText v-model="filters.search" placeholder="Puesto, empresa o palabra clave"
                            class="w-full" @keyup.enter="search" />
                    </div>
                    <div class="w-full md:w-48">
                        <InputText v-model="filters.city" placeholder="Ciudad" class="w-full" @keyup.enter="search" />
                    </div>
                    <Button label="Buscar" icon="pi pi-search" @click="search" :loading="isLoading"
                        style="background-color:#013C7B;border-color:#013C7B;" />
                    <Button v-if="hasActiveFilters" label="Limpiar" icon="pi pi-times" outlined severity="secondary"
                        @click="clearFilters" />
                </div>

                <!-- Filtros avanzados -->
                <div class="flex flex-wrap gap-3 mt-4">
                    <Select v-model="filters.category_id" :options="jobCategories" option-label="name" option-value="id"
                        placeholder="Categoría" :show-clear="true" class="w-44"
                        @change="search" />
                    <Select v-model="filters.contract_type_id" :options="contractTypes" option-label="name"
                        option-value="id" placeholder="Contrato" :show-clear="true" class="w-44"
                        @change="search" />
                    <Select v-model="filters.modality_id" :options="modalityTypes" option-label="name"
                        option-value="id" placeholder="Modalidad" :show-clear="true" class="w-44"
                        @change="search" />
                    <Select v-model="filters.workday_type_id" :options="workdayTypes" option-label="name"
                        option-value="id" placeholder="Jornada" :show-clear="true" class="w-44"
                        @change="search" />
                    <div class="flex items-center gap-2 px-3 py-2 border rounded-lg bg-white cursor-pointer"
                        :class="filters.is_adapted ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @click="toggleAdapted">
                        <Checkbox v-model="filters.is_adapted" binary inputId="adapted" />
                        <label for="adapted" class="text-sm cursor-pointer whitespace-nowrap">Puesto adaptado</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="container mx-auto px-6 py-8">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-600">
                    <span v-if="!isLoading">
                        <strong>{{ offers.meta?.total ?? offers.data?.length ?? 0 }}</strong> ofertas encontradas
                    </span>
                    <Skeleton v-else width="12rem" height="1rem" />
                </p>
            </div>

            <!-- Grid de ofertas -->
            <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="n in 6" :key="n" class="bg-white rounded-xl p-5 border border-gray-200">
                    <Skeleton height="1.5rem" class="mb-3" />
                    <Skeleton width="60%" height="1rem" class="mb-2" />
                    <Skeleton width="40%" height="1rem" class="mb-4" />
                    <Skeleton height="2.5rem" />
                </div>
            </div>

            <div v-else-if="offers.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="offer in offers.data" :key="offer.id"
                    class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:border-blue-200 transition-all duration-200 flex flex-col">
                    <!-- Cabecera -->
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <i class="pi pi-building text-gray-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm leading-snug truncate">{{ offer.title }}</h3>
                            <p class="text-xs text-[#013C7B] font-medium mt-0.5">{{ offer.company?.name ?? 'Empresa' }}</p>
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span v-if="offer.city"
                            class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                            <i class="pi pi-map-marker text-xs"></i> {{ offer.city }}
                        </span>
                        <span v-if="offer.modality?.name"
                            class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                            <i class="pi pi-desktop text-xs"></i> {{ offer.modality.name }}
                        </span>
                        <span v-if="offer.contract_type?.name"
                            class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                            <i class="pi pi-file-edit text-xs"></i> {{ offer.contract_type.name }}
                        </span>
                        <span v-if="offer.is_adapted"
                            class="inline-flex items-center gap-1 text-xs text-white bg-green-500 px-2 py-0.5 rounded-full font-medium">
                            <i class="pi pi-check text-xs"></i> Adaptado
                        </span>
                    </div>

                    <!-- Descripción -->
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4 flex-1">{{ offer.description }}</p>

                    <!-- Categoría + Fecha + Botón -->
                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100">
                        <div>
                            <span v-if="offer.category?.name"
                                class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                {{ offer.category.name }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ timeAgo(offer.created_at) }}</p>
                        </div>
                        <router-link :to="{ name: 'ofertas.show', params: { id: offer.id } }">
                            <Button label="Ver oferta" size="small"
                                style="background-color:#013C7B;border-color:#013C7B;" />
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-16 text-gray-400">
                <i class="pi pi-briefcase text-5xl mb-4 block"></i>
                <p class="text-lg font-medium text-gray-600">No se encontraron ofertas</p>
                <p class="text-sm mt-2">Prueba a cambiar los filtros de búsqueda</p>
                <Button label="Ver todas las ofertas" class="mt-4" outlined @click="clearFilters" />
            </div>

            <!-- Paginación -->
            <div v-if="offers.meta?.last_page > 1" class="flex justify-center mt-8 gap-2">
                <Button icon="pi pi-chevron-left" outlined size="small" :disabled="currentPage === 1"
                    @click="goToPage(currentPage - 1)" />
                <Button v-for="p in pageNumbers" :key="p" :label="String(p)" size="small"
                    :outlined="p !== currentPage"
                    :style="p === currentPage ? 'background-color:#013C7B;border-color:#013C7B;' : ''"
                    @click="goToPage(p)" />
                <Button icon="pi pi-chevron-right" outlined size="small"
                    :disabled="currentPage === offers.meta?.last_page"
                    @click="goToPage(currentPage + 1)" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import usePublicOffers from '@/composables/usePublicOffers'

const route = useRoute()
const router = useRouter()
const { offers, isLoading, getOffers } = usePublicOffers()

const jobCategories = ref([])
const contractTypes = ref([])
const modalityTypes = ref([])
const workdayTypes = ref([])
const currentPage = ref(1)

const filters = ref({
    search: '',
    city: '',
    category_id: null,
    contract_type_id: null,
    modality_id: null,
    workday_type_id: null,
    is_adapted: false,
})

const hasActiveFilters = computed(() =>
    filters.value.search || filters.value.city || filters.value.category_id ||
    filters.value.contract_type_id || filters.value.modality_id ||
    filters.value.workday_type_id || filters.value.is_adapted
)

const pageNumbers = computed(() => {
    const total = offers.value.meta?.last_page ?? 1
    const current = currentPage.value
    const pages = []
    for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) pages.push(i)
    return pages
})

const timeAgo = (dateStr) => {
    if (!dateStr) return ''
    const diff = Date.now() - new Date(dateStr)
    const days = Math.floor(diff / 86400000)
    if (days === 0) return 'Hoy'
    if (days === 1) return 'Ayer'
    if (days < 7) return `Hace ${days} días`
    if (days < 30) return `Hace ${Math.floor(days / 7)} semanas`
    return `Hace ${Math.floor(days / 30)} meses`
}

const search = () => {
    currentPage.value = 1
    loadOffers()
    updateQueryParams()
}

const clearFilters = () => {
    filters.value = { search: '', city: '', category_id: null, contract_type_id: null, modality_id: null, workday_type_id: null, is_adapted: false }
    currentPage.value = 1
    loadOffers()
    router.replace({ name: 'ofertas.index' })
}

const toggleAdapted = () => {
    filters.value.is_adapted = !filters.value.is_adapted
    search()
}

const goToPage = (p) => {
    currentPage.value = p
    loadOffers()
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const loadOffers = () => {
    getOffers({ ...filters.value, is_adapted: filters.value.is_adapted || undefined, page: currentPage.value, per_page: 12 })
}

const updateQueryParams = () => {
    const q = {}
    if (filters.value.search) q.search = filters.value.search
    if (filters.value.city) q.city = filters.value.city
    router.replace({ name: 'ofertas.index', query: q })
}

const loadLookups = async () => {
    const [cats, contracts, modalities, workdays] = await Promise.all([
        axios.get('/api/job-categories').then(r => r.data),
        axios.get('/api/contract-types').then(r => r.data),
        axios.get('/api/modality-types').then(r => r.data),
        axios.get('/api/workday-types').then(r => r.data),
    ])
    jobCategories.value = cats
    contractTypes.value = contracts
    modalityTypes.value = modalities
    workdayTypes.value = workdays
}

onMounted(() => {
    // Leer query params de la URL (búsqueda desde la home)
    if (route.query.search) filters.value.search = route.query.search
    if (route.query.city) filters.value.city = route.query.city
    loadOffers()
    loadLookups()
})
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
