<template>
    <div class="grid grid-cols-1 gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                Bienvenido, {{ auth.user?.name }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Panel de gestión de tu empresa</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-briefcase text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalOffers }}</p>
                            <p class="text-sm text-gray-500">Ofertas totales</p>
                            <p class="text-xs text-green-600 font-medium mt-0.5">{{ publishedOffers }} publicadas</p>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-users text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalApplications }}</p>
                            <p class="text-sm text-gray-500">Candidaturas recibidas</p>
                            <p class="text-xs text-yellow-600 font-medium mt-0.5">{{ pendingApplications }} pendientes de revisión</p>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-building text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ acceptedApplications }}</p>
                            <p class="text-sm text-gray-500">Candidaturas aceptadas</p>
                            <router-link to="/empresa/candidaturas" class="text-xs text-purple-600 hover:underline font-medium mt-0.5 block">
                                Ver todas →
                            </router-link>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Accesos rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card>
                <template #title>Acciones rápidas</template>
                <template #content>
                    <div class="flex flex-col gap-3">
                        <router-link to="/empresa/ofertas">
                            <Button label="Gestionar mis ofertas" icon="pi pi-briefcase" class="w-full" outlined />
                        </router-link>
                        <router-link to="/empresa/candidaturas">
                            <Button label="Ver candidaturas" icon="pi pi-users" class="w-full" outlined severity="success" />
                        </router-link>
                        <router-link to="/empresa/perfil">
                            <Button label="Editar perfil de empresa" icon="pi pi-building" class="w-full" outlined severity="secondary" />
                        </router-link>
                    </div>
                </template>
            </Card>

            <Card>
                <template #title>Últimas candidaturas</template>
                <template #content>
                    <div v-if="recentApplications.length" class="flex flex-col gap-2">
                        <div v-for="app in recentApplications" :key="app.id"
                            class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-surface-700 last:border-0">
                            <div>
                                <p class="text-sm font-medium">{{ candidateName(app) }}</p>
                                <p class="text-xs text-gray-500">{{ app.offer?.title ?? '-' }}</p>
                            </div>
                            <Tag :value="appStatusLabel(app.status)" :severity="appStatusSeverity(app.status)" size="small" />
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400 text-center py-4">Todavía no hay candidaturas</p>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import useJobOffers from '@/composables/useJobOffers'
import useJobApplications from '@/composables/useJobApplications'
import { authStore } from '@/store/auth'

const auth = authStore()
const { offers, getOffers } = useJobOffers()
const { applications, getApplicationsForOffers } = useJobApplications()

const appStatusOptions = [
    { label: 'Enviada', value: 'SENT', severity: 'info' },
    { label: 'En revisión', value: 'IN_REVIEW', severity: 'warn' },
    { label: 'Aceptada', value: 'ACCEPTED', severity: 'success' },
    { label: 'Rechazada', value: 'REJECTED', severity: 'danger' },
    { label: 'Cancelada', value: 'CANCELED', severity: 'secondary' },
]
const appStatusLabel = (s) => appStatusOptions.find(o => o.value === s)?.label ?? s
const appStatusSeverity = (s) => appStatusOptions.find(o => o.value === s)?.severity ?? 'secondary'
const candidateName = (app) => {
    const c = app.candidate
    if (!c) return 'Candidato'
    return [c.name, c.surname1].filter(Boolean).join(' ') || c.email || 'Sin nombre'
}

const totalOffers = computed(() => offers.value?.data?.length ?? 0)
const publishedOffers = computed(() => offers.value?.data?.filter(o => o.status === 'PUBLISHED').length ?? 0)
const totalApplications = computed(() => applications.value.length)
const pendingApplications = computed(() => applications.value.filter(a => a.status === 'SENT' || a.status === 'IN_REVIEW').length)
const acceptedApplications = computed(() => applications.value.filter(a => a.status === 'ACCEPTED').length)
const recentApplications = computed(() => [...applications.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5))

onMounted(async () => {
    await getOffers({ company_user_id: auth.user.id })
    const ids = (offers.value?.data ?? []).map(o => o.id)
    await getApplicationsForOffers(ids)
})
</script>
