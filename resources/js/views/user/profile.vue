<template>
    <div class="min-h-screen bg-white py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="container mx-auto max-w-6xl">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8">Mi Cuenta</h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Avatar Section -->
                <div class="lg:col-span-1">
                    <Card class="rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-shadow">
                        <template #title>
                            <span class="text-lg font-bold text-gray-900">Avatar</span>
                        </template>
                        <template #content>
                            <div class="flex flex-col items-center space-y-6">
                                <Avatar 
                                    :image="user.avatar || 'https://bootdey.com/img/Content/avatar/avatar7.png'" 
                                    class="w-32 h-32" 
                                    size="xlarge" 
                                    shape="circle"
                                />
                                <!-- File Upload -->
                                <FileUpload
                                    name="picture"
                                    url="/api/users/updateimg"
                                    @before-upload="onBeforeUpload"
                                    @upload="onTemplatedUpload($event)"
                                    accept="image/*"
                                    :maxFileSize="1500000"
                                    @select="onSelectedFiles"
                                    mode="basic"
                                    :auto="true"
                                    chooseLabel="Cambiar Avatar"
                                    class="w-full p-button-outlined"
                                />
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Personal Data Section -->
                <div class="lg:col-span-3">
                    <Card class="rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-shadow h-full">
                        <template #title>
                            <span class="text-lg font-bold text-gray-900">Datos Personales</span>
                        </template>
                        <template #content>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="field">
                                    <label class="font-bold block mb-3 text-gray-700 text-sm">Nombre</label>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                        {{ user.name }}
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="font-bold block mb-3 text-gray-700 text-sm">Email</label>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                        {{ user.email }}
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="font-bold block mb-3 text-gray-700 text-sm">Primer Apellido</label>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                        {{ user.surname1 || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="font-bold block mb-3 text-gray-700 text-sm">Segundo Apellido</label>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                                        {{ user.surname2 || '-' }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <!-- Mis Inscripciones -->
            <section class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Mis Candidaturas</h2>
                <div v-if="myApplications.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <JobCardComponent 
                        v-for="offer in myApplications" 
                        :key="offer.id"
                        :job="offer"
                        :is-applied="true"
                    />
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <i class="pi pi-inbox text-3xl mb-4 block opacity-40"></i>
                    <p>Aún no has solicitado ninguna oferta</p>
                </div>
            </section>

            <!-- Ofertas Recomendadas -->
            <section class="mt-12 pb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Ofertas para Ti</h2>
                <div v-if="recommendedOffers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <JobCardComponent 
                        v-for="offer in recommendedOffers" 
                        :key="offer.id"
                        :job="offer"
                        :is-applied="false"
                        @apply="handleApplyOffer(offer)"
                    />
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <i class="pi pi-search text-3xl mb-4 block opacity-40"></i>
                    <p>No hay ofertas recomendadas en este momento</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { usePrimeVue } from 'primevue/config';
import useUsers from "@/composables/users";
import useJobApplications from "@/composables/jobApplications";
import { authStore } from "@/store/auth";
import JobCardComponent from "@/components/JobCardComponent.vue";

const auth = authStore();
const $primevue = usePrimeVue();
const { getUser, user } = useUsers();
const { myApplications, recommendedOffers, getMyApplications, getRecommendedOffers } = useJobApplications();

onMounted(() => {
    getUser(auth.user.id)
    getMyApplications(auth.user.id)
    getRecommendedOffers(auth.user.id)
})

const onBeforeUpload = (event) => {
    event.formData.append('id', user.value.id)
};

const onTemplatedUpload = (event) => {
    // Recargar usuario para actualizar avatar
    getUser(auth.user.id);
};

const onSelectedFiles = (event) => {
    // Lógica adicional si es necesaria
};

const handleApplyOffer = (offer) => {
    console.log('Aplicar a oferta:', offer);
    // Implementar lógica para aplicar a oferta
};
</script>

<style scoped>
:deep(.p-button-outlined) {
    color: #013C7B !important;
    border-color: #013C7B !important;
}

:deep(.p-button-outlined:hover) {
    background-color: #013C7B !important;
    color: white !important;
    border-color: #013C7B !important;
}
</style>
