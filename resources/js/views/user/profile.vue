<template>
    <div class="grid grid-cols-1 gap-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna izquierda: Avatar -->
            <div class="lg:col-span-1">
                <Card>
                    <template #content>
                        <div class="flex flex-col items-center gap-4">
                            <Avatar
                                :image="authUser.avatar"
                                :label="!authUser.avatar ? initials : undefined"
                                shape="circle"
                                class="w-24 h-24 text-2xl"
                            />
                            <div class="text-center">
                                <p class="font-semibold text-gray-900 dark:text-white text-lg">{{ authUser.name }}</p>
                                <p class="text-sm text-gray-500">{{ authUser.email }}</p>
                                <Tag :value="roleName" severity="info" class="mt-2" size="small" />
                            </div>
                            <FileUpload
                                name="picture"
                                url="/api/users/updateimg"
                                @before-upload="onBeforeUpload"
                                @upload="onAvatarUploaded"
                                accept="image/*"
                                :maxFileSize="1500000"
                                mode="basic"
                                :auto="true"
                                chooseLabel="Cambiar foto"
                                class="w-full"
                            />
                        </div>
                    </template>
                </Card>

                <!-- Accesos rápidos -->
                <Card class="mt-4">
                    <template #title>Accesos rápidos</template>
                    <template #content>
                        <div class="flex flex-col gap-2">
                            <router-link to="/app/cv">
                                <Button label="Mi Currículum" icon="pi pi-file" class="w-full" outlined severity="primary" size="small" />
                            </router-link>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Columna derecha: Datos y perfil -->
            <div class="lg:col-span-2 flex flex-col gap-6">

                <!-- Datos básicos de cuenta -->
                <Card>
                    <template #title>Datos de la cuenta</template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                <InputText v-model="accountForm.name" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <InputText :value="authUser.email" disabled class="w-full opacity-60" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Primer apellido</label>
                                <InputText v-model="accountForm.surname1" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Segundo apellido</label>
                                <InputText v-model="accountForm.surname2" class="w-full" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <Button label="Guardar datos" icon="pi pi-check" :loading="savingAccount" @click="saveAccount" size="small" />
                        </div>
                    </template>
                </Card>

                <!-- Perfil candidato -->
                <Card>
                    <template #title>Perfil de candidato</template>
                    <template #subtitle>Información visible para las empresas</template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></label>
                                <InputText v-model="profileForm.first_name" class="w-full" placeholder="Tu nombre" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos <span class="text-red-500">*</span></label>
                                <InputText v-model="profileForm.last_name" class="w-full" placeholder="Tus apellidos" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                                <InputText v-model="profileForm.phone" class="w-full" placeholder="+34 600 000 000" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                                <InputText v-model="profileForm.city" class="w-full" placeholder="Ej: Madrid" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de discapacidad</label>
                                <Select v-model="profileForm.disability_type_id" :options="disabilityTypes"
                                    option-label="name" option-value="id" placeholder="Seleccionar..."
                                    :show-clear="true" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Grado de discapacidad (%)
                                    <span v-if="profileForm.disability_degree" class="text-blue-600 font-semibold ml-1">{{ profileForm.disability_degree }}%</span>
                                </label>
                                <input type="range" v-model.number="profileForm.disability_degree"
                                    min="0" max="100" step="1"
                                    class="w-full h-2 rounded-full accent-blue-600 cursor-pointer" />
                            </div>
                            <div class="md:col-span-2 flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Necesidades de accesibilidad</label>
                                <Textarea v-model="profileForm.accessibility_needs" rows="2"
                                    placeholder="Describe cualquier adaptación necesaria..." class="w-full" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <Button label="Guardar perfil" icon="pi pi-check" :loading="profileLoading" @click="saveProfileData" size="small" />
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { authStore } from '@/store/auth'
import useCandidateProfile from '@/composables/useCandidateProfile'
import { useToast } from '@/composables/useToast'

const auth = authStore()
const authUser = computed(() => auth.user)
const toast = useToast()
const { profile, isLoading: profileLoading, getProfile, saveProfile } = useCandidateProfile()

const savingAccount = ref(false)
const disabilityTypes = ref([])

const initials = computed(() => {
    const n = authUser.value?.name ?? ''
    return n.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || 'U'
})

const roleName = computed(() => authUser.value?.roles?.[0]?.name ?? 'usuario')

const accountForm = ref({ name: '', surname1: '', surname2: '' })
const profileForm = ref({
    first_name: '', last_name: '', phone: '', city: '',
    disability_type_id: null, disability_degree: 0, accessibility_needs: ''
})

const loadDisabilityTypes = async () => {
    const r = await axios.get('/api/disability-types')
    disabilityTypes.value = r.data
}

const syncForms = () => {
    const u = authUser.value
    accountForm.value = { name: u?.name ?? '', surname1: u?.surname1 ?? '', surname2: u?.surname2 ?? '' }

    if (profile.value) {
        profileForm.value = {
            first_name: profile.value.first_name ?? '',
            last_name: profile.value.last_name ?? '',
            phone: profile.value.phone ?? '',
            city: profile.value.city ?? '',
            disability_type_id: profile.value.disability_type_id ?? null,
            disability_degree: profile.value.disability_degree ?? 0,
            accessibility_needs: profile.value.accessibility_needs ?? ''
        }
    }
}

const saveAccount = async () => {
    savingAccount.value = true
    try {
        const response = await axios.put(`/api/users/${authUser.value.id}`, {
            ...accountForm.value,
            role_id: authUser.value.roles?.map(r => r.id) ?? []
        })
        await auth.getUser()
        toast.crud.saved('Datos de cuenta')
    } catch {
        toast.error('Error', 'No se pudieron guardar los datos')
    } finally {
        savingAccount.value = false
    }
}

const saveProfileData = async () => {
    if (!profileForm.value.first_name || !profileForm.value.last_name) {
        toast.error('Error de validación', 'El nombre y apellidos son obligatorios')
        return
    }
    await saveProfile(authUser.value.id, profileForm.value)
}

const onBeforeUpload = (event) => {
    event.formData.append('id', authUser.value.id)
}

const onAvatarUploaded = async () => {
    await auth.getUser()
}

onMounted(async () => {
    await getProfile(authUser.value.id)
    await loadDisabilityTypes()
    syncForms()
})
</script>
