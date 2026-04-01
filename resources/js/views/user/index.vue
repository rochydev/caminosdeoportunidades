<template>
    <div class="grid grid-cols-1 gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                Bienvenido/a, {{ auth.user?.name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Panel de candidato</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-user text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Mi Perfil</p>
                            <p class="text-xs text-gray-500 mb-1">Datos personales y perfil</p>
                            <router-link to="/app/profile" class="text-xs text-blue-600 hover:underline font-medium">
                                Ir al perfil →
                            </router-link>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-file text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Mi Currículum</p>
                            <p class="text-xs text-gray-500 mb-1">Experiencias y formación</p>
                            <router-link to="/app/cv" class="text-xs text-green-600 hover:underline font-medium">
                                Ver CV →
                            </router-link>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-send text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Mis Candidaturas</p>
                            <p class="text-xs text-gray-500 mb-1">Ofertas en las que has aplicado</p>
                            <span class="text-xs text-gray-400 font-medium">Próximamente</span>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card>
                <template #title>Completa tu perfil</template>
                <template #content>
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3 p-3 rounded-lg" :class="hasProfile ? 'bg-green-50 dark:bg-green-900/10' : 'bg-gray-50 dark:bg-surface-800'">
                            <i :class="hasProfile ? 'pi pi-check-circle text-green-500' : 'pi pi-circle text-gray-400'" class="text-lg"></i>
                            <div>
                                <p class="text-sm font-medium">Datos de perfil</p>
                                <p class="text-xs text-gray-500">Nombre, teléfono, ciudad...</p>
                            </div>
                            <router-link v-if="!hasProfile" to="/app/profile" class="ml-auto text-xs text-blue-600 hover:underline">Completar</router-link>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg" :class="hasCv ? 'bg-green-50 dark:bg-green-900/10' : 'bg-gray-50 dark:bg-surface-800'">
                            <i :class="hasCv ? 'pi pi-check-circle text-green-500' : 'pi pi-circle text-gray-400'" class="text-lg"></i>
                            <div>
                                <p class="text-sm font-medium">Currículum</p>
                                <p class="text-xs text-gray-500">Experiencias y formación</p>
                            </div>
                            <router-link v-if="!hasCv" to="/app/cv" class="ml-auto text-xs text-blue-600 hover:underline">Completar</router-link>
                        </div>
                    </div>
                </template>
            </Card>

            <Card>
                <template #title>Acciones rápidas</template>
                <template #content>
                    <div class="flex flex-col gap-2">
                        <router-link to="/app/profile">
                            <Button label="Editar perfil" icon="pi pi-user" class="w-full" outlined size="small" />
                        </router-link>
                        <router-link to="/app/cv">
                            <Button label="Gestionar mi CV" icon="pi pi-file" class="w-full" outlined severity="success" size="small" />
                        </router-link>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { authStore } from '@/store/auth'

const auth = authStore()
const hasProfile = ref(false)
const hasCv = ref(false)

onMounted(async () => {
    try {
        const p = await axios.get(`/api/candidate-profiles/${auth.user.id}`)
        hasProfile.value = !!p.data?.data
    } catch { hasProfile.value = false }

    try {
        const c = await axios.get('/api/candidate-cv')
        hasCv.value = !!c.data?.data
    } catch { hasCv.value = false }
})
</script>
