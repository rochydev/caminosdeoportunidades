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

            <!-- CV Section -->
            <section class="mt-8">
                <Card class="rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-shadow">
                    <template #title>
                        <span class="text-lg font-bold text-gray-900">Currículum Vitae</span>
                    </template>
                    <template #content>
                        <div class="flex flex-col gap-4">
                            <!-- CV actual -->
                            <div v-if="user.cv" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <i class="pi pi-file-pdf text-red-500 text-2xl"></i>
                                <span class="flex-1 font-medium truncate text-gray-900">{{ user.cv.name }}</span>
                                <Button
                                    icon="pi pi-eye"
                                    label="Ver CV"
                                    severity="secondary"
                                    text
                                    @click="cvDialogVisible = true"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    severity="danger"
                                    text
                                    rounded
                                    :loading="isDeletingCv"
                                    @click="handleDeleteCv"
                                    v-tooltip.top="'Eliminar CV'"
                                />
                            </div>
                            <div v-else class="text-gray-500 text-sm">
                                No tienes ningún currículum subido.
                            </div>

                            <!-- Upload -->
                            <div>
                                <FileUpload
                                    name="cv"
                                    url="/api/users/upload-cv"
                                    @before-upload="onBeforeCvUpload"
                                    @upload="onCvUpload"
                                    @error="onCvUploadError"
                                    accept=".pdf,application/pdf"
                                    :maxFileSize="10485760"
                                    mode="basic"
                                    :auto="true"
                                    chooseLabel="Subir CV (PDF, máx. 10 MB)"
                                />
                            </div>
                        </div>
                    </template>
                </Card>
            </section>

            <!-- CV Viewer Dialog -->
            <Dialog
                v-model:visible="cvDialogVisible"
                :header="user.cv?.name"
                modal
                maximizable
                :style="{ width: '85vw', height: '90vh' }"
                contentStyle="height: 100%; padding: 0;"
            >
                <iframe
                    v-if="cvDialogVisible"
                    :src="user.cv?.url"
                    class="w-full h-full border-0"
                    style="min-height: 75vh;"
                />
            </Dialog>

            <!-- Mis Candidaturas -->
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
import axios from 'axios';
import useUsers from "@/composables/users";
import useJobApplications from "@/composables/jobApplications";
import { authStore } from "@/store/auth";
import { useToast } from "@/composables/useToast";
import JobCardComponent from "@/components/JobCardComponent.vue";

const auth = authStore();
const $primevue = usePrimeVue();
const { getUser, user } = useUsers();
const { myApplications, recommendedOffers, getMyApplications, getRecommendedOffers } = useJobApplications();
const toast = useToast();
const isDeletingCv = ref(false);
const cvDialogVisible = ref(false);

onMounted(() => {
    getUser(auth.user.id)
    getMyApplications(auth.user.id)
    getRecommendedOffers(auth.user.id)
})

// ---- Avatar ----
const onBeforeUpload = (event) => {
    event.formData.append('id', user.value.id)
};

const onTemplatedUpload = () => {
    getUser(auth.user.id);
};

const onSelectedFiles = () => {};

// ---- CV ----
const onBeforeCvUpload = (event) => {
    event.formData.append('id', user.value.id)
};

const onCvUpload = () => {
    getUser(auth.user.id);
    toast.success('CV subido', 'Tu currículum se ha guardado correctamente.');
};

const onCvUploadError = () => {
    toast.error('Error al subir', 'No se pudo subir el CV. Asegúrate de que es un PDF válido y no supera 10 MB.');
};

const handleDeleteCv = async () => {
    isDeletingCv.value = true;
    try {
        await axios.delete('/api/users/delete-cv', { data: { id: user.value.id } });
        await getUser(auth.user.id);
        toast.success('CV eliminado', 'Tu currículum ha sido eliminado.');
    } catch {
        toast.error('Error', 'No se pudo eliminar el CV.');
    } finally {
        isDeletingCv.value = false;
    }
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
