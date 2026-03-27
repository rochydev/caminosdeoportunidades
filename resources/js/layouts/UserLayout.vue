<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const items = ref([
    {
        label: 'Empleo',
        items: [
            {
                label: 'Buscar Ofertas',
                icon: 'pi pi-search',
                route: '/app/jobs'
            },
        ]
    },
    {
        label: 'Cuenta',
        items: [
            {
                label: 'Mi Perfil',
                icon: 'pi pi-user',
                route: '/app/profile'
            }
        ]
    }
]);
</script>
