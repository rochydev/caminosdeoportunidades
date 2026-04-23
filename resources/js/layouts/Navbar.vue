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
                <div class="flex items-center gap-4">
                    <!-- Guest: botones de acceso -->
                    <template v-if="!isLoggedIn">
                        <router-link :to="{ name: 'auth.login.empresa' }">
                            <button class="border-2 border-[#013C7B] text-[#013C7B] hover:bg-[#013C7B] hover:text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                ACCESO EMPRESAS
                            </button>
                        </router-link>
                        <router-link to="/login">
                            <button class="bg-[#E91E63] hover:bg-[#C2185B] text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                ACCESO CANDIDATOS
                            </button>
                        </router-link>
                    </template>

                    <!-- Logueado: avatar + menú -->
                    <template v-else>
                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                @click="toggle"
                                class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors">
                                <Avatar
                                    :image="currentUser.avatar || undefined"
                                    :label="!currentUser.avatar ? userInitials : undefined"
                                    shape="circle"
                                    size="normal"
                                    class="w-9 h-9 shrink-0"
                                />
                                <div class="text-left hidden xl:block">
                                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ currentUser.name }}</p>
                                    <p class="text-xs text-gray-500 leading-tight">{{ userRoleLabel }}</p>
                                </div>
                                <i class="pi pi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': menuOpen }"></i>
                            </button>
                            <Menu ref="menu" :model="items" popup @show="menuOpen = true" @hide="menuOpen = false" />
                        </div>
                    </template>
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
                        <!-- Guest -->
                        <template v-if="!isLoggedIn">
                            <router-link :to="{ name: 'auth.login.empresa' }" @click="visibleMobileMenu = false">
                                <button class="w-full border-2 border-[#013C7B] text-[#013C7B] hover:bg-[#013C7B] hover:text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-all duration-200">
                                    ACCESO EMPRESAS
                                </button>
                            </router-link>
                            <router-link to="/login" @click="visibleMobileMenu = false">
                                <button class="w-full bg-[#E91E63] hover:bg-[#C2185B] text-white font-bold text-sm uppercase px-6 py-3 rounded-md transition-all duration-200 shadow-md hover:shadow-lg">
                                    ACCESO CANDIDATOS
                                </button>
                            </router-link>
                        </template>

                        <!-- Logueado -->
                        <template v-else>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <Avatar
                                    :image="currentUser.avatar || undefined"
                                    :label="!currentUser.avatar ? userInitials : undefined"
                                    shape="circle" size="normal" class="w-10 h-10 shrink-0"
                                />
                                <div>
                                    <p class="font-semibold text-sm text-gray-900">{{ currentUser.name }}</p>
                                    <p class="text-xs text-gray-500">{{ userRoleLabel }}</p>
                                </div>
                            </div>
                            <button v-for="menuItem in mobileMenuItems" :key="menuItem.label"
                                @click="menuItem.command(); visibleMobileMenu = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left w-full text-sm font-medium"
                                :class="menuItem.class">
                                <i :class="menuItem.icon" class="w-4"></i>
                                {{ menuItem.label }}
                            </button>
                        </template>
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
const auth = authStore();
const menu = ref();
const menuOpen = ref(false);
const visibleMobileMenu = ref(false);
const isScrolled = ref(false);
const isDesktop = ref(window.innerWidth >= 992);

const { logout } = useAuth();

const navLinks = [
    { label: 'Ofertas', route: '/ofertas' },
    { label: 'Propósito', route: '/proposito' },
    { label: 'Contacto', route: '/contacto' }
];

const isAuthPage = computed(() =>
    ['auth.login', 'auth.register', 'auth.forgot-password', 'auth.login.empresa'].includes(route.name)
);

const isLoggedIn = computed(() => !!auth.authenticated && !!auth.user?.id);
const currentUser = computed(() => auth.user ?? {});

const userInitials = computed(() => {
    const name = currentUser.value?.name ?? '';
    return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || 'U';
});

const userRoleLabel = computed(() => {
    const role = currentUser.value?.roles?.[0]?.name ?? '';
    const map = { admin: 'Administrador', company: 'Empresa', candidate: 'Candidato' };
    return map[role] ?? role;
});

const isAdmin   = computed(() => currentUser.value?.roles?.some(r => r.name?.toLowerCase().includes('admin')) ?? false);
const isCompany = computed(() => currentUser.value?.roles?.some(r => r.name?.toLowerCase() === 'company') ?? false);

const items = computed(() => [{
    items: [
        {
            label: 'Mi Perfil',
            icon: 'pi pi-user',
            command: () => router.push(isCompany.value ? '/empresa/perfil' : '/app/profile')
        },
        {
            label: 'Panel Admin',
            icon: 'pi pi-shield',
            visible: isAdmin.value,
            command: () => router.push('/admin')
        },
        {
            label: 'Panel Empresa',
            icon: 'pi pi-building',
            visible: isCompany.value && !isAdmin.value,
            command: () => router.push('/empresa')
        },
        {
            label: 'Mi Panel',
            icon: 'pi pi-th-large',
            visible: !isCompany.value && !isAdmin.value,
            command: () => router.push('/app')
        },
        { separator: true },
        {
            label: 'Cerrar sesión',
            icon: 'pi pi-power-off',
            class: 'logout-item',
            command: () => handleLogout()
        }
    ]
}]);

// Items planos para el menú mobile
const mobileMenuItems = computed(() => [
    {
        label: 'Mi Perfil',
        icon: 'pi pi-user',
        command: () => router.push(isCompany.value ? '/empresa/perfil' : '/app/profile')
    },
    ...(isAdmin.value ? [{ label: 'Panel Admin', icon: 'pi pi-shield', command: () => router.push('/admin') }] : []),
    ...(isCompany.value && !isAdmin.value ? [{ label: 'Panel Empresa', icon: 'pi pi-building', command: () => router.push('/empresa') }] : []),
    ...(!isCompany.value && !isAdmin.value ? [{ label: 'Mi Panel', icon: 'pi pi-th-large', command: () => router.push('/app') }] : []),
    { label: 'Cerrar sesión', icon: 'pi pi-power-off', class: 'text-red-500', command: () => handleLogout() },
]);

const toggle = (event) => menu.value.toggle(event);

const handleLogout = () => {
    visibleMobileMenu.value = false;
    logout();
};

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
const handleResize = () => { isDesktop.value = window.innerWidth >= 992; };

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
:deep(.logout-item) {
    color: #ef4444 !important;
}
:deep(.logout-item .p-menuitem-icon),
:deep(.logout-item .p-menuitem-text) {
    color: #ef4444 !important;
}
</style>

