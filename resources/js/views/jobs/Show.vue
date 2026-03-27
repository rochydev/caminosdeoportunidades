<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="container mx-auto max-w-4xl">

            <!-- Back -->
            <Button
                label="Volver a ofertas"
                icon="pi pi-arrow-left"
                text
                class="mb-6 text-[#013C7B]"
                @click="router.push({ name: 'jobs.index' })"
            />

            <!-- Loading -->
            <div v-if="isLoading" class="space-y-4">
                <Skeleton height="200px" class="rounded-xl" />
                <Skeleton height="300px" class="rounded-xl" />
            </div>

            <div v-else-if="offer">
                <!-- Header card -->
                <Card class="rounded-xl border border-gray-200 shadow-sm mb-6">
                    <template #content>
                        <div class="flex flex-col sm:flex-row gap-6 items-start">
                            <!-- Company logo -->
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                                <img
                                    v-if="offer.company?.avatar"
                                    :src="offer.company.avatar"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-blue-50">
                                    <i class="pi pi-building text-[#013C7B] text-2xl opacity-40"></i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900">{{ offer.title }}</h1>
                                        <p class="text-gray-500 mt-1">{{ offer.company?.name }}</p>
                                    </div>
                                    <span v-if="offer.is_adapted" class="text-sm bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-medium">
                                        Puesto adaptado
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span v-if="offer.category?.name" class="text-sm bg-blue-50 text-[#013C7B] px-3 py-1 rounded-full font-medium">
                                        {{ offer.category.name }}
                                    </span>
                                    <span v-if="offer.city" class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                        <i class="pi pi-map-marker mr-1 text-xs"></i>{{ offer.city }}
                                    </span>
                                    <span v-if="offer.contractType?.name" class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                        {{ offer.contractType.name }}
                                    </span>
                                    <span v-if="offer.workdayType?.name" class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                        {{ offer.workdayType.name }}
                                    </span>
                                    <span v-if="offer.modality?.name" class="text-sm bg-green-50 text-green-700 px-3 py-1 rounded-full">
                                        {{ offer.modality.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Apply button -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div v-if="applicationStatus">
                                <div class="flex items-center gap-3 p-4 rounded-lg" :class="statusBg">
                                    <i class="pi pi-check-circle text-xl"></i>
                                    <div>
                                        <p class="font-semibold">Candidatura {{ statusLabel }}</p>
                                        <p class="text-sm opacity-80">Ya has aplicado a esta oferta</p>
                                    </div>
                                </div>
                            </div>
                            <Button
                                v-else
                                label="Enviar candidatura"
                                icon="pi pi-send"
                                size="large"
                                :loading="isApplying"
                                @click="handleApply"
                                :style="{ background: '#013C7B', borderColor: '#013C7B' }"
                            />
                        </div>
                    </template>
                </Card>

                <!-- Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main content -->
                    <div class="lg:col-span-2 space-y-6">
                        <Card class="rounded-xl border border-gray-200 shadow-sm">
                            <template #title>Descripción del puesto</template>
                            <template #content>
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ offer.description }}</p>
                            </template>
                        </Card>

                        <Card v-if="offer.requirements" class="rounded-xl border border-gray-200 shadow-sm">
                            <template #title>Requisitos</template>
                            <template #content>
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ offer.requirements }}</p>
                            </template>
                        </Card>

                        <Card v-if="offer.adaptations" class="rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-purple-400">
                            <template #title>
                                <span class="flex items-center gap-2">
                                    <i class="pi pi-heart text-purple-500"></i>
                                    Adaptaciones disponibles
                                </span>
                            </template>
                            <template #content>
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ offer.adaptations }}</p>
                            </template>
                        </Card>
                    </div>

                    <!-- Sidebar info -->
                    <div class="space-y-4">
                        <Card class="rounded-xl border border-gray-200 shadow-sm">
                            <template #title>Información</template>
                            <template #content>
                                <ul class="space-y-3 text-sm">
                                    <li v-if="offer.city" class="flex items-center gap-2 text-gray-600">
                                        <i class="pi pi-map-marker text-[#013C7B]"></i>
                                        {{ offer.city }}
                                    </li>
                                    <li v-if="offer.contractType?.name" class="flex items-center gap-2 text-gray-600">
                                        <i class="pi pi-file text-[#013C7B]"></i>
                                        {{ offer.contractType.name }}
                                    </li>
                                    <li v-if="offer.workdayType?.name" class="flex items-center gap-2 text-gray-600">
                                        <i class="pi pi-clock text-[#013C7B]"></i>
                                        {{ offer.workdayType.name }}
                                    </li>
                                    <li v-if="offer.modality?.name" class="flex items-center gap-2 text-gray-600">
                                        <i class="pi pi-desktop text-[#013C7B]"></i>
                                        {{ offer.modality.name }}
                                    </li>
                                    <li class="flex items-center gap-2 text-gray-600">
                                        <i class="pi pi-calendar text-[#013C7B]"></i>
                                        Publicada {{ formatDate(offer.created_at) }}
                                    </li>
                                </ul>
                            </template>
                        </Card>

                        <Card v-if="offer.tags?.length" class="rounded-xl border border-gray-200 shadow-sm">
                            <template #title>Etiquetas</template>
                            <template #content>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="tag in offer.tags"
                                        :key="tag.id"
                                        class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded"
                                    >
                                        {{ tag.name }}
                                    </span>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Not found -->
            <div v-else class="text-center py-16 text-gray-500">
                <i class="pi pi-exclamation-circle text-4xl mb-4 block opacity-30"></i>
                <p>No se encontró la oferta</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import useJobOffers from '@/composables/useJobOffers'
import useJobApplications from '@/composables/jobApplications'
import { authStore } from '@/store/auth'

const router = useRouter()
const route = useRoute()
const auth = authStore()
const { offer, isLoading, getOffer } = useJobOffers()
const { applyToOffer, isApplying } = useJobApplications()

const applicationStatus = ref(null)

const statusMap = {
    SENT: { label: 'enviada', bg: 'bg-blue-50 text-blue-700' },
    IN_REVIEW: { label: 'en revisión', bg: 'bg-yellow-50 text-yellow-700' },
    ACCEPTED: { label: 'aceptada', bg: 'bg-green-50 text-green-700' },
    REJECTED: { label: 'rechazada', bg: 'bg-red-50 text-red-700' },
    CANCELED: { label: 'cancelada', bg: 'bg-gray-100 text-gray-600' },
}

const statusLabel = computed(() => statusMap[applicationStatus.value]?.label ?? applicationStatus.value)
const statusBg = computed(() => statusMap[applicationStatus.value]?.bg ?? 'bg-gray-100 text-gray-600')

onMounted(async () => {
    await getOffer(route.params.id)
    await checkIfApplied()
})

const checkIfApplied = async () => {
    try {
        const { data } = await axios.get('/api/job-applications', {
            params: { offer_id: route.params.id, candidate_user_id: auth.user.id, per_page: 1 }
        })
        if (data.data?.length > 0) {
            applicationStatus.value = data.data[0].status
        }
    } catch {
        // ignore
    }
}

const handleApply = async () => {
    try {
        const app = await applyToOffer(route.params.id, auth.user.id)
        if (app) applicationStatus.value = 'SENT'
    } catch {
        // error handled in composable
    }
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>
