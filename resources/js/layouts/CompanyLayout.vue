<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const items = ref([
    {
        label: 'Ofertas de empleo',
        items: [
            {
                label: 'Mis Ofertas',
                icon: 'pi pi-briefcase',
                route: '/empresa/ofertas'
            },
        ]
    },
    {
        label: 'Candidaturas',
        items: [
            {
                label: 'Ver Candidaturas',
                icon: 'pi pi-users',
                route: '/empresa/candidaturas'
            }
        ]
    },
    {
        label: 'Cuenta',
        items: [
            {
                label: 'Perfil Empresa',
                icon: 'pi pi-building',
                route: '/empresa/perfil'
            }
        ]
    }
]);
</script>
