<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const items = ref([
    {
        label: 'Empresa',
        items: [
            {
                label: 'Dashboard',
                icon: 'pi pi-home',
                route: '/empresa/dashboard'
            },
            {
                label: 'Mis Ofertas',
                icon: 'pi pi-briefcase',
                route: '/empresa/dashboard/ofertas'
            },
        ]
    },
    {
        label: 'Cuenta',
        items: [
            {
                label: 'Perfil',
                icon: 'pi pi-building',
                route: '/empresa/dashboard/perfil'
            }
        ]
    }
]);
</script>
