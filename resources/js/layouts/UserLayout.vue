<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const items = ref([
    {
        label: 'Mi espacio',
        items: [
            { label: 'Inicio',           icon: 'pi pi-home',       route: '/app' },
            { label: 'Mi Perfil',        icon: 'pi pi-user',       route: '/app/profile' },
            { label: 'Mi Currículum',    icon: 'pi pi-file',       route: '/app/cv' },
            { label: 'Mis Candidaturas', icon: 'pi pi-send',       route: '/app/candidaturas' },
        ]
    },
    {
        label: 'Explorar',
        items: [
            { label: 'Ofertas de empleo', icon: 'pi pi-briefcase', route: '/ofertas' },
        ]
    },
]);
</script>
