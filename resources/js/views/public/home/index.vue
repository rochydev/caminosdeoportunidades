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
                    Impulsa tu carrera trabajando en una empresa líder
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="empresa in empresas" 
                        :key="empresa.id"
                        class="rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
                    >
                        <div class="h-40 overflow-hidden">
                            <img 
                                :src="empresa.image" 
                                :alt="empresa.name" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-4 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex-shrink-0 flex items-center justify-center">
                                <i class="pi pi-building text-gray-400"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ empresa.name }}</p>
                                <p class="text-sm text-[#013C7B]">{{ empresa.ofertas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-10">
                    <button 
                        class="flex items-center gap-2 px-8 py-3 border-2 border-gray-300 rounded-full text-gray-700 font-bold hover:border-[#013C7B] hover:text-[#013C7B] transition-colors"
                    >
                        <i class="pi pi-refresh"></i>
                        MOSTRAR MÁS EMPRESAS
                    </button>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authStore } from "@/store/auth";

const router = useRouter();
const searchKeyword = ref('');
const searchCity = ref('');

const goToOffers = () => {
    const query = {};
    if (searchKeyword.value) query.search = searchKeyword.value;
    if (searchCity.value) query.city = searchCity.value;
    router.push({ name: 'ofertas.index', query });
};

const empresas = ref([
    { id: 1, name: 'Empresa 1', ofertas: '4 ofertas', image: '/images/imagen-placeholder-temporal.png' },
    { id: 2, name: 'Empresa 2', ofertas: '437 ofertas', image: '/images/imagen-placeholder-temporal.png' },
    { id: 3, name: 'Empresa 3', ofertas: '1 oferta', image: '/images/imagen-placeholder-temporal.png' },
    { id: 4, name: 'Empresa 4', ofertas: '11 ofertas', image: '/images/imagen-placeholder-temporal.png' },
    { id: 5, name: 'Empresa 5', ofertas: '7 ofertas', image: '/images/imagen-placeholder-temporal.png' },
    { id: 6, name: 'Empresa 6', ofertas: '1 oferta', image: '/images/imagen-placeholder-temporal.png' },
]);

const sectores = ref([
    { name: 'Informática', count: '2.706', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Sanidad y Salud', count: '3.673', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Comercial y Ventas', count: '6.786', image: '/images/imagen-placeholder-temporal.png' },
    { name: 'Directivos', count: '782', image: '/images/imagen-placeholder-temporal.png' },
]);
</script>
