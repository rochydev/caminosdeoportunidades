<template>
    <div class="bg-[#FAFAFA] min-h-screen">
        <div class="container mx-auto px-6 py-8 max-w-5xl">

            <!-- Back -->
            <router-link :to="{ name: 'ofertas.index' }"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-[#013C7B] mb-6 font-medium">
                <i class="pi pi-arrow-left"></i> Volver a ofertas
            </router-link>

            <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-gray-200">
                    <Skeleton height="2rem" class="mb-4" />
                    <Skeleton width="50%" height="1rem" class="mb-6" />
                    <Skeleton height="8rem" />
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <Skeleton height="3rem" class="mb-3" />
                    <Skeleton height="1rem" class="mb-2" />
                    <Skeleton height="1rem" />
                </div>
            </div>

            <div v-else-if="offer" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Columna principal -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <!-- Imagen destacada -->
                    <div v-if="offerImage" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <img :src="offerImage" :alt="offer.title" class="w-full h-64 object-cover" />
                    </div>

                    <!-- Cabecera oferta -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="offerImage" :src="offerImage" :alt="offer.title" class="w-full h-full object-cover" />
                                <i v-else class="pi pi-building text-2xl text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ offer.title }}</h1>
                                <p class="text-[#013C7B] font-semibold mt-1">{{ offer.company?.name ?? 'Empresa' }}</p>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span v-if="offer.city"
                                        class="inline-flex items-center gap-1 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="pi pi-map-marker"></i> {{ offer.city }}
                                    </span>
                                    <span v-if="offer.modality?.name"
                                        class="inline-flex items-center gap-1 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="pi pi-desktop"></i> {{ offer.modality.name }}
                                    </span>
                                    <span v-if="offer.contract_type?.name"
                                        class="inline-flex items-center gap-1 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="pi pi-file-edit"></i> {{ offer.contract_type.name }}
                                    </span>
                                    <span v-if="offer.workday_type?.name"
                                        class="inline-flex items-center gap-1 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="pi pi-clock"></i> {{ offer.workday_type.name }}
                                    </span>
                                    <span v-if="offer.category?.name"
                                        class="inline-flex items-center gap-1 text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full font-medium">
                                        {{ offer.category.name }}
                                    </span>
                                    <span v-if="offer.is_adapted"
                                        class="inline-flex items-center gap-1 text-sm text-white bg-green-500 px-3 py-1 rounded-full font-medium">
                                        <i class="pi pi-check"></i> Puesto adaptado
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Descripción del puesto</h2>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ offer.description }}</p>
                    </div>

                    <!-- Requisitos -->
                    <div v-if="offer.requirements" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Requisitos</h2>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ offer.requirements }}</p>
                    </div>

                    <!-- Adaptaciones -->
                    <div v-if="offer.adaptations" class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="pi pi-heart text-green-500"></i> Adaptaciones disponibles
                        </h2>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ offer.adaptations }}</p>
                    </div>
                </div>

                <!-- Columna lateral: solicitud -->
                <div class="flex flex-col gap-4">
                    <!-- Panel de solicitud -->
                    <div class="bg-white rounded-xl border border-gray-200 p-5 sticky top-24">
                        <p class="text-xs text-gray-400 mb-4">Publicada el {{ formatDate(offer.created_at) }}</p>

                        <!-- No logueado -->
                        <template v-if="!isAuthenticated">
                            <p class="text-sm text-gray-600 mb-4 text-center">
                                Inicia sesión para solicitar esta oferta
                            </p>
                            <router-link :to="{ name: 'auth.login', query: { redirect: `/ofertas/${offer.id}` } }">
                                <Button label="Iniciar sesión para solicitar" class="w-full"
                                    style="background-color:#E91E63;border-color:#E91E63;" />
                            </router-link>
                            <router-link :to="{ name: 'auth.register' }" class="block mt-2">
                                <Button label="¿No tienes cuenta? Regístrate" class="w-full" outlined severity="secondary" size="small" />
                            </router-link>
                        </template>

                        <!-- Es empresa -->
                        <template v-else-if="isCompany">
                            <div class="text-center py-4">
                                <i class="pi pi-building text-3xl text-blue-400 mb-2 block"></i>
                                <p class="text-sm text-gray-500">Las empresas no pueden solicitar ofertas</p>
                            </div>
                        </template>

                        <!-- Ya aplicó -->
                        <template v-else-if="myApplication">
                            <div class="text-center mb-4">
                                <i class="pi pi-check-circle text-3xl text-green-500 mb-2 block"></i>
                                <p class="font-semibold text-gray-900">Ya has solicitado esta oferta</p>
                                <div class="mt-2">
                                    <Tag :value="appStatusLabel(myApplication.status)"
                                        :severity="appStatusSeverity(myApplication.status)" />
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ formatDate(myApplication.created_at) }}</p>
                            </div>
                            <Button v-if="myApplication.status !== 'CANCELED'" label="Cancelar candidatura"
                                outlined severity="danger" class="w-full" size="small"
                                :loading="applyLoading" @click="handleCancel" />
                            <router-link to="/app/candidaturas" class="block mt-2">
                                <Button label="Ver mis candidaturas" outlined severity="secondary" class="w-full" size="small" />
                            </router-link>
                        </template>

                        <!-- Puede solicitar -->
                        <template v-else>
                            <Button label="Solicitar oferta" icon="pi pi-send" class="w-full mb-2"
                                :loading="applyLoading" @click="handleApply"
                                style="background-color:#013C7B;border-color:#013C7B;" />
                            <router-link to="/app/candidaturas" class="block">
                                <Button label="Ver mis candidaturas" outlined severity="secondary" class="w-full" size="small" />
                            </router-link>
                        </template>
                    </div>

                    <!-- Info empresa -->
                    <div v-if="offer.company" class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Sobre la empresa</h3>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                <i class="pi pi-building text-gray-400"></i>
                            </div>
                            <p class="font-semibold text-sm text-gray-900">{{ offer.company.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Not found -->
            <div v-else class="text-center py-16">
                <i class="pi pi-exclamation-triangle text-4xl text-yellow-400 mb-4 block"></i>
                <p class="text-lg font-medium text-gray-700">Oferta no encontrada</p>
                <router-link :to="{ name: 'ofertas.index' }" class="block mt-4">
                    <Button label="Ver otras ofertas" outlined />
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import usePublicOffers from '@/composables/usePublicOffers'
import useApplications from '@/composables/useApplications'
import { authStore } from '@/store/auth'

const route = useRoute()
const router = useRouter()
const auth = authStore()
const { offer, isLoading, getOffer } = usePublicOffers()
const { applications, getMyApplications, apply, cancelApplication, getApplicationForOffer } = useApplications()

const applyLoading = ref(false)

const isAuthenticated = computed(() => !!auth.authenticated && !!auth.user?.id)
const isCompany = computed(() => auth.user?.roles?.some(r => r.name === 'company') ?? false)
const myApplication = computed(() => getApplicationForOffer(Number(route.params.id)))
const offerImage = computed(() => offer.value?.image_url
    ?? offer.value?.media?.[0]?.original_url
    ?? offer.value?.media?.[0]?.url
    ?? null)

const appStatusOptions = [
    { label: 'Enviada', value: 'SENT', severity: 'info' },
    { label: 'En revisión', value: 'IN_REVIEW', severity: 'warn' },
    { label: 'Aceptada', value: 'ACCEPTED', severity: 'success' },
    { label: 'Rechazada', value: 'REJECTED', severity: 'danger' },
    { label: 'Cancelada', value: 'CANCELED', severity: 'secondary' },
]
const appStatusLabel = (s) => appStatusOptions.find(o => o.value === s)?.label ?? s
const appStatusSeverity = (s) => appStatusOptions.find(o => o.value === s)?.severity ?? 'secondary'
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'

const handleApply = async () => {
    applyLoading.value = true
    await apply(Number(route.params.id), auth.user.id)
    applyLoading.value = false
}

const handleCancel = async () => {
    if (!myApplication.value) return
    applyLoading.value = true
    await cancelApplication(myApplication.value.id)
    applyLoading.value = false
}

onMounted(async () => {
    await getOffer(route.params.id)
    if (isAuthenticated.value && !isCompany.value) {
        await getMyApplications(auth.user.id)
    }
})
</script>
