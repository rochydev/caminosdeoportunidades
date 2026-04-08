<template>
    <div>

        <div class="relative w-full min-h-[400px] flex items-start bg-white pt-24 overflow-hidden z-0">
            <div class="absolute inset-0 z-0 flex justify-end items-end mt-6">
                <img 
                    src="/images/header_background.svg" 
                    alt="Background" 
                    class="h-[110%] w-auto object-contain object-right-bottom"
                />
            </div>
            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-8">
                        Siempre a mejor
                    </h1>
                    <div class="flex flex-col md:flex-row items-end gap-4">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Busco ofertas de...</label>
                            <InputText
                                v-model="searchKeyword"
                                placeholder="Puesto, empresa o palabra clave"
                                class="w-full p-3 rounded-lg border-gray-300 shadow-sm"
                                @keyup.enter="goToOffers"
                            />
                        </div>
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-600 mb-2">en...</label>
                            <InputText
                                v-model="searchCity"
                                placeholder="Toda España"
                                class="w-full p-3 rounded-lg border-gray-300 shadow-sm"
                                @keyup.enter="goToOffers"
                            />
                        </div>
                        <Button
                            label="BUSCAR"
                            class="w-full md:w-auto border-none font-bold px-8 py-3 rounded-lg shadow-md transition-colors"
                            style="background-color: #013C7B !important; color: white;"
                            @click="goToOffers"
                        />
                    </div>
                </div>
            </div>
        </div>


        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12">
                    Oportunidades laborales disponibles
                </h2>

                <!-- Resultados -->
                <div class="container mx-auto px-6 py-8">
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sm text-gray-600">
                            <span v-if="!isLoading">
                                <strong>{{ offers.total ?? offers.data?.length ?? 0 }}</strong> ofertas encontradas
                            </span>
                            <Skeleton v-else width="12rem" height="1rem" />
                        </p>
                    </div>

                    <!-- Grid de ofertas -->
                    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="n in 6" :key="n" class="bg-white rounded-xl p-5 border border-gray-200">
                        <Skeleton height="10rem" class="mb-3" />
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
                        <Button label="Ver todas las ofertas" class="mt-4" outlined @click="goToOffers" />
                    </div>

                    <!-- Botón Mostrar más -->
                    <div v-if="currentPage < offers.last_page" class="flex justify-center mt-8">
                        <button
                            @click="loadMoreOffers"
                            class="inline-flex items-center gap-2 px-6 py-2 text-white font-bold rounded-md text-sm transition-colors cursor-pointer hover:opacity-90"
                            style="background-color: #013C7B;"
                        >
                            <i class="pi pi-refresh"></i>
                            MOSTRAR MÁS OFERTAS
                        </button>
                    </div>
                </div>
            </div>
        </section>


        <section class="py-8 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="rounded-2xl overflow-hidden" style="background-color: #C5D5E4;">

                        <div class="flex flex-col md:hidden">
                            <div class="p-6 flex flex-col items-center text-center">
                                <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">¿Necesitas un coach laboral?</p>
                                <p class="text-lg font-medium text-gray-900 mb-4">
                                    Te damos consejos para mejorar tu carrera profesional
                                </p>
                                <a 
                                    href="#" 
                                    class="inline-block px-6 py-2 text-white font-bold rounded-md text-sm transition-colors"
                                    style="background-color: #013C7B;"
                                >
                                    IR AL BLOG
                                </a>
                            </div>
                            <div class="flex justify-center">
                                <img 
                                    src="/images/girl-wheel-chair-blue.svg" 
                                    alt="Coach laboral" 
                                    class="h-48 object-contain"
                                />
                            </div>
                        </div>

                        <div class="hidden md:block relative min-h-[280px]">
                            <img 
                                src="/images/girl-wheel-chair-blue.svg" 
                                alt="Coach laboral" 
                                class="absolute bottom-0 left-0 h-[260px] object-contain"
                            />
                            <div class="ml-auto w-3/5 p-8 flex flex-col justify-center min-h-[280px]">
                                <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">¿Necesitas un coach laboral?</p>
                                <p class="text-xl font-medium text-gray-900 mb-5">
                                    Te damos consejos para mejorar tu carrera profesional
                                </p>
                                <div>
                                    <a 
                                        href="#" 
                                        class="inline-block px-6 py-3 text-white font-bold rounded-md text-sm transition-colors"
                                        style="background-color: #013C7B;"
                                    >
                                        IR AL BLOG
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="rounded-2xl overflow-hidden" style="background-color: #F5E6C8;">
                        <div class="flex flex-col md:hidden">
                            <div class="p-6 flex flex-col items-center text-center">
                                <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mejora tu perfil profesional</p>
                                <p class="text-lg font-medium text-gray-900 mb-4">
                                    Adquiere nuevos conocimientos con nuestros cursos de formación
                                </p>
                                <a 
                                    href="#" 
                                    class="inline-block px-6 py-2 font-bold rounded-md text-sm border-2 transition-colors"
                                    style="color: #013C7B; border-color: #013C7B;"
                                >
                                    VER CURSOS
                                </a>
                            </div>
                            <div class="flex justify-center">
                                <img 
                                    src="/images/girl-book-yellow.svg" 
                                    alt="Formación" 
                                    class="h-48 object-contain"
                                />
                            </div>
                        </div>
                        <div class="hidden md:block relative min-h-[280px]">
                            <img 
                                src="/images/girl-book-yellow.svg" 
                                alt="Formación" 
                                class="absolute bottom-0 left-0 h-[260px] object-contain"
                            />
                            <div class="ml-auto w-3/5 p-8 flex flex-col justify-center min-h-[280px]">
                                <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mejora tu perfil profesional</p>
                                <p class="text-xl font-medium text-gray-900 mb-5">
                                    Adquiere nuevos conocimientos con nuestros cursos de formación
                                </p>
                                <div>
                                    <a 
                                        href="#" 
                                        class="inline-block px-6 py-3 font-bold rounded-md text-sm border-2 transition-colors"
                                        style="color: #013C7B; border-color: #013C7B;"
                                    >
                                        VER CURSOS
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="py-16 mt-8" style="background-color: #FFF8EC;">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                    <div class="w-full md:w-1/2">
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight text-center md:text-left" style="color: #013C7B;">
                            Cuando se generan oportunidades, se construye futuro.
                        </h2>
                    </div>
                    <div class="w-full md:w-1/2 flex justify-center relative">
                        <img 
                            src="/images/boy-blind.svg" 
                            alt="Cuando se generan oportunidades" 
                            class="h-72 md:h-96 object-contain"
                        />
                    </div>
                </div>
            </div>
        </section>


        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                        Sectores con más oportunidades
                    </h2>
                    <a href="#" class="text-[#013C7B] font-bold text-sm hover:underline">+ SECTORES</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div 
                        v-for="sector in sectores" 
                        :key="sector.name"
                        class="cursor-pointer group"
                    >
                        <div class="rounded-xl overflow-hidden mb-3 border border-gray-200 group-hover:shadow-lg transition-shadow">
                            <img 
                                :src="sector.image" 
                                :alt="sector.name" 
                                class="w-full h-36 md:h-44 object-cover"
                            />
                        </div>
                        <p class="text-center text-sm font-bold text-[#013C7B] uppercase tracking-wide">{{ sector.name }}</p>
                        <p class="text-center text-sm text-gray-500 mt-1">{{ sector.count }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authStore } from "@/store/auth";
import usePublicOffers from '@/composables/usePublicOffers';

const router = useRouter();
const searchKeyword = ref('');
const searchCity = ref('');
const { offers, isLoading, getOffers } = usePublicOffers();
const currentPage = ref(1);

const goToOffers = () => {
    const query = {};
    if (searchKeyword.value) query.search = searchKeyword.value;
    if (searchCity.value) query.city = searchCity.value;
    router.push({ name: 'ofertas.index', query });
};

const pageNumbers = computed(() => {
    const total = offers.value.last_page ?? 1
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

const loadOffers = async () => {
    const result = await getOffers({ per_page: 3, page: currentPage.value })
    if (currentPage.value === 1) {
        offers.value.data = result.data
    } else {
        offers.value.data.push(...result.data)
    }
    offers.value.last_page = result.last_page
    offers.value.total = result.total
};

const loadMoreOffers = () => {
    currentPage.value += 1
    loadOffers()
}

onMounted(() => {
    loadOffers();
});

const sectores = ref([
    { name: 'Informática', count: '2.706', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Sanidad y Salud', count: '3.673', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Comercial y Ventas', count: '6.786', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Directivos', count: '782', image: '/images/imagen-placeholder-temporal.png' },
]);
</script>
