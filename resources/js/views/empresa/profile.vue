<template>
    <div class="grid grid-cols-1 gap-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Perfil de Empresa</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna izquierda: Avatar / Logo -->
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
                                <p class="font-semibold text-gray-900 dark:text-white text-lg">{{ profileForm.company_name || authUser.name }}</p>
                                <p class="text-sm text-gray-500">{{ authUser.email }}</p>
                                <Tag value="Empresa" severity="info" class="mt-2" size="small" />
                                <div v-if="profile?.validation_status" class="mt-2">
                                    <Tag
                                        :value="validationLabel(profile.validation_status)"
                                        :severity="validationSeverity(profile.validation_status)"
                                        size="small"
                                    />
                                </div>
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
                                chooseLabel="Cambiar logo"
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
                            <router-link to="/empresa/ofertas">
                                <Button label="Mis ofertas" icon="pi pi-briefcase" class="w-full" outlined severity="primary" size="small" />
                            </router-link>
                            <router-link to="/empresa/candidaturas">
                                <Button label="Candidaturas recibidas" icon="pi pi-send" class="w-full" outlined severity="primary" size="small" />
                            </router-link>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Columna derecha: Datos -->
            <div class="lg:col-span-2 flex flex-col gap-6">

                <!-- Datos básicos de cuenta -->
                <Card>
                    <template #title>Datos de la cuenta</template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de contacto</label>
                                <InputText v-model="accountForm.name" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <InputText :value="authUser.email" disabled class="w-full opacity-60" />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <Button label="Guardar datos" icon="pi pi-check" :loading="savingAccount" @click="saveAccount" size="small" />
                        </div>
                    </template>
                </Card>

                <!-- Perfil de empresa -->
                <Card>
                    <template #title>Información de la empresa</template>
                    <template #subtitle>Datos visibles para los candidatos</template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2 flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la empresa <span class="text-red-500">*</span></label>
                                <InputText v-model="profileForm.company_name" class="w-full" placeholder="Ej: Acme Inc." />
                            </div>
                            <div class="md:col-span-2 flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                <Textarea v-model="profileForm.description" rows="3"
                                    placeholder="Describe brevemente tu empresa..." class="w-full" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sector</label>
                                <InputText v-model="profileForm.sector" class="w-full" placeholder="Ej: Tecnología" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                                <InputText v-model="profileForm.city" class="w-full" placeholder="Ej: Barcelona" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono de contacto</label>
                                <InputText v-model="profileForm.contact_phone" class="w-full" placeholder="+34 900 000 000" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sitio web</label>
                                <InputText v-model="profileForm.website" class="w-full" placeholder="https://..." />
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <Button label="Guardar perfil" icon="pi pi-check" :loading="profileLoading" @click="saveProfileData" size="small" />
                        </div>
                    </template>
                </Card>

                <!-- Compromiso con la inclusión -->
                <Card>
                    <template #title>Compromiso con la inclusión</template>
                    <template #subtitle>Estas opciones mejoran la visibilidad de tu empresa para candidatos con discapacidad</template>
                    <template #content>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="profileForm.offers_adapted_positions" inputId="adapted" binary />
                                <label for="adapted" class="text-sm cursor-pointer">
                                    Ofrecemos puestos adaptados para personas con discapacidad
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="profileForm.offers_remote_work" inputId="remote" binary />
                                <label for="remote" class="text-sm cursor-pointer">
                                    Ofrecemos trabajo en remoto
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="profileForm.offers_reasonable_adjustments" inputId="adjustments" binary />
                                <label for="adjustments" class="text-sm cursor-pointer">
                                    Aplicamos ajustes razonables en el puesto
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <Button label="Guardar compromiso" icon="pi pi-check" :loading="profileLoading" @click="saveProfileData" size="small" />
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
import useCompanyProfile from '@/composables/useCompanyProfile'
import { useToast } from '@/composables/useToast'

const auth = authStore()
const authUser = computed(() => auth.user)
const toast = useToast()
const { profile, isLoading: profileLoading, getProfile, saveProfile } = useCompanyProfile()

const savingAccount = ref(false)

const initials = computed(() => {
    const n = authUser.value?.name ?? ''
    return n.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || 'E'
})

const accountForm = ref({ name: '' })
const profileForm = ref({
    company_name: '',
    description: '',
    sector: '',
    city: '',
    contact_phone: '',
    website: '',
    offers_adapted_positions: false,
    offers_remote_work: false,
    offers_reasonable_adjustments: false,
})

const validationLabels = {
    PENDING: 'Pendiente de validación',
    VALIDATED: 'Validada',
    REJECTED: 'Rechazada',
    BLOCKED: 'Bloqueada',
}
const validationSeverities = {
    PENDING: 'warn',
    VALIDATED: 'success',
    REJECTED: 'danger',
    BLOCKED: 'danger',
}
const validationLabel = (s) => validationLabels[s] ?? s
const validationSeverity = (s) => validationSeverities[s] ?? 'secondary'

const syncForms = () => {
    const u = authUser.value
    accountForm.value = { name: u?.name ?? '' }

    if (profile.value) {
        profileForm.value = {
            company_name: profile.value.company_name ?? '',
            description: profile.value.description ?? '',
            sector: profile.value.sector ?? '',
            city: profile.value.city ?? '',
            contact_phone: profile.value.contact_phone ?? '',
            website: profile.value.website ?? '',
            offers_adapted_positions: !!profile.value.offers_adapted_positions,
            offers_remote_work: !!profile.value.offers_remote_work,
            offers_reasonable_adjustments: !!profile.value.offers_reasonable_adjustments,
        }
    }
}

const saveAccount = async () => {
    savingAccount.value = true
    try {
        await axios.put(`/api/users/${authUser.value.id}`, {
            name: accountForm.value.name,
            surname1: authUser.value.surname1 ?? '',
            surname2: authUser.value.surname2 ?? '',
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
    if (!profileForm.value.company_name) {
        toast.error('Error de validación', 'El nombre de la empresa es obligatorio')
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
    syncForms()
})
</script>
