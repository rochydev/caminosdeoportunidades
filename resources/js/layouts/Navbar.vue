<template>
    <div 
        class="fixed w-full z-50 bg-white transition-all duration-300"
        :class="{ 'shadow-sm': isScrolled }">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <router-link to="/" class="flex items-center gap-2">
                <img src="/images/logo.svg" alt="Caminos de Oportunidades" class="h-12 w-auto"/>
            </router-link>

            <!-- Mobile Menu Button -->
            <button
                v-if="!isDesktop"
                @click="visibleMobileMenu = true"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="pi pi-bars text-2xl text-[#013C7B]"></i>
            </button>

            <!-- Desktop Menu -->
            <div v-if="isDesktop" class="flex items-center justify-between flex-1 ml-12">
                <!-- Nav Links (Hidden on Auth pages) -->
                <div class="flex items-center gap-8" v-if="!isAuthPage">
                    <router-link 
                        v-for="link in navLinks" 
                        :key="link.label" 
                        :to="link.route" 
                        class="text-gray-600 hover:text-[#013C7B] font-medium text-lg transition-colors"
                    >
                        {{ link.label }}
                    </router-link>
                </div>
                <div v-else></div> <!-- Spacer -->
                
                <!-- Actions -->
                <div class="flex items-center gap-6">
                    <!-- Accesso Empresas -->
                    <router-link :to="{ name: 'auth.login.company' }" class="text-[#013C7B] font-bold text-sm uppercase hover:underline">
                        ACCESO EMPRESAS
                    </router-link>
                    
                    <!-- Accesso Candidatos -->
                    <router-link to="/login">
                        <button class="bg-[#E91E63] hover:bg-[#C2185B] text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-colors shadow-md">
                            ACCESO CANDIDATOS
                        </button>
                    </router-link>

                    <!-- User Menu (if logged in) -->
                    <div v-if="authStore().user?.name" class="ml-4">
                         <button 
                            type="button" 
                            @click="toggle"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <Avatar :image="authStore().user.avatar" :label="authStore().user.name[0]" shape="circle" size="small" />
                            <i class="pi pi-chevron-down text-xs"></i>
                        </button>
                        <Menu ref="menu" :model="items" popup />
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div v-if="visibleMobileMenu" class="fixed inset-0 z-50 lg:hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="visibleMobileMenu = false"></div>
            
            <!-- Panel -->
            <div 
                class="absolute right-0 top-0 h-full w-full sm:w-80 bg-white shadow-2xl"
                @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <img src="/images/logo.svg" alt="logo" class="h-8"/>
                    <button 
                        @click="visibleMobileMenu = false"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="pi pi-times text-xl text-[#013C7B]"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex flex-col gap-6 p-6">
                    <!-- Nav Links -->
                    <div class="flex flex-col gap-4" v-if="!isAuthPage">
                        <router-link 
                            v-for="link in navLinks"
                            :key="link.label"
                            :to="link.route" 
                            @click="visibleMobileMenu = false"
                            class="text-gray-600 font-medium text-lg hover:text-[#013C7B]"
                        >
                            {{ link.label }}
                        </router-link>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <!-- Auth Actions -->
                    <div class="flex flex-col gap-4">
                        <router-link :to="{ name: 'auth.login.company' }" class="text-[#013C7B] font-bold text-center uppercase" @click="visibleMobileMenu = false">
                            ACCESO EMPRESAS
                        </router-link>
                        <router-link to="/login" @click="visibleMobileMenu = false">
                            <button class="w-full bg-[#E91E63] hover:bg-[#C2185B] text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-colors shadow-md">
                                ACCESO CANDIDATOS
                            </button>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Spacer to prevent content overlap -->
    <div class="h-24"></div>
</template>

<script setup>
import { useLayout } from "@/composables/layout.js";
import useAuth from "@/composables/auth";
import { authStore } from "../store/auth";
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();
const menu = ref();
const visibleMobileMenu = ref(false);
const isScrolled = ref(false);
const isDesktop = ref(window.innerWidth >= 992);

const { logout } = useAuth();

// Navigation Links matching the design
const navLinks = [
    { label: 'Ofertas', route: '#' },
    { label: 'Proposito', route: '#' },
    { label: 'Contacto', route: '#' }
];

// Check if current page is Login or Register to hide nav links
const isAuthPage = computed(() => {
    return ['auth.login', 'auth.register', 'auth.forgot-password', 'auth.login.company', 'auth.register.company'].includes(route.name);
});

const items = computed(() => [
    {
        items: [
            { label: 'Perfil', icon: 'pi pi-user', command: () => router.push('/app/profile') },
            { 
                label: 'Panel Admin', 
                icon: 'pi pi-cog', 
                route: '/admin', 
                visible: authStore().user?.roles?.some(r => r.name.includes('admin')) || false
            },
            { label: 'Mi Panel', icon: 'pi pi-th-large', route: '/app' },
            { separator: true },
            {
                label: 'Cerrar sesión',
                icon: 'pi pi-power-off',
                class: 'text-red-500',
                command: () => {
                    handleLogout()
                }
            }
        ]
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

const handleLogout = () => {
    visibleMobileMenu.value = false;
    logout();
}

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
}

const handleResize = () => {
    isDesktop.value = window.innerWidth >= 992;
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
});
</script>

