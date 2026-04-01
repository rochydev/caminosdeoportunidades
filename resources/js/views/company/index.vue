<template>
    <div class="p-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-blue-50 p-3 rounded-full mb-4">
                    <i class="pi pi-briefcase text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Ofertas Publicadas</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.published }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-emerald-50 p-3 rounded-full mb-4">
                    <i class="pi pi-users text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Candidaturas Nuevas</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.newApplications }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-yellow-50 p-3 rounded-full mb-4">
                    <i class="pi pi-file-edit text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Borradores</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.drafts }}</p>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center cursor-pointer hover:shadow-md transition-shadow"
                @click="router.push({ name: 'company.offers.create' })"
            >
                <div class="text-white bg-[#013C7B] p-3 rounded-full mb-4">
                    <i class="pi pi-plus text-2xl"></i>
                </div>
                <h3 class="text-gray-900 text-sm font-bold mt-1">Publicar nueva oferta</h3>
            </div>
        </div>

        <!-- Recent applications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Candidaturas Recientes</h2>
                <Button
                    label="Ver todas las ofertas"
                    text
                    class="text-[#013C7B]"
                    @click="router.push({ name: 'company.offers.index' })"
                />
            </div>

            <div v-if="isLoading" class="p-6 space-y-3">
                <Skeleton v-for="i in 3" :key="i" height="60px" class="rounded-lg" />
            </div>

            <div v-else-if="recentApplications.length === 0" class="p-6 text-center text-gray-500 py-12">
                <i class="pi pi-inbox text-4xl mb-3 text-gray-300"></i>
                <p>Las últimas candidaturas de talento aparecerán aquí.</p>
                <Button
                    label="Crea tu primera oferta"
                    text
                    class="mt-2 text-[#013C7B]"
                    @click="router.push({ name: 'company.offers.create' })"
                />
            </div>

            <div v-else class="divide-y divide-gray-50">
                <div
                    v-for="app in recentApplications"
                    :key="app.id"
                    class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition-colors cursor-pointer"
                    @click="router.push({ name: 'company.offers.show', params: { id: app.offer?.id } })"
                >
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="pi pi-user text-[#013C7B]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ app.candidate?.name }} {{ app.candidate?.surname1 }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ app.offer?.title }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="appStatusBadge(app.status)" class="text-xs font-medium px-2 py-1 rounded whitespace-nowrap">
                            {{ appStatusLabel(app.status) }}
                        </span>
                        <span class="text-xs text-gray-400 hidden sm:block">{{ formatDate(app.created_at) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { authStore } from '@/store/auth'

const router = useRouter()
const auth = authStore()

const myOffers = ref([])
const recentApplications = ref([])
const isLoading = ref(false)

const stats = computed(() => ({
    published: myOffers.value.filter(o => o.status === 'PUBLISHED').length,
    drafts: myOffers.value.filter(o => o.status === 'DRAFT').length,
    newApplications: recentApplications.value.filter(a => a.status === 'SENT').length,
}))

onMounted(async () => {
    isLoading.value = true
    try {
        const [offersRes, appsRes] = await Promise.all([
            axios.get('/api/job-offers', { params: { company_user_id: auth.user.id, per_page: 100 } }),
            axios.get('/api/job-applications', { params: { per_page: 10 } }),
        ])
        myOffers.value = offersRes.data.data ?? []

        // Filter applications belonging to this company's offers
        const offerIds = new Set(myOffers.value.map(o => o.id))
        recentApplications.value = (appsRes.data.data ?? []).filter(a => offerIds.has(a.offer_id))
    } catch {
        // silently fail
    } finally {
        isLoading.value = false
    }
})

const appStatusLabel = (s) => ({
    SENT: 'Nueva', IN_REVIEW: 'En revisión', ACCEPTED: 'Aceptada', REJECTED: 'Rechazada', CANCELED: 'Cancelada',
}[s] ?? s)

const appStatusBadge = (s) => ({
    SENT: 'bg-blue-100 text-blue-700',
    IN_REVIEW: 'bg-yellow-100 text-yellow-700',
    ACCEPTED: 'bg-green-100 text-green-700',
    REJECTED: 'bg-red-100 text-red-700',
    CANCELED: 'bg-gray-100 text-gray-700',
}[s] ?? 'bg-gray-100 text-gray-700')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-ES') : '—'
</script>
