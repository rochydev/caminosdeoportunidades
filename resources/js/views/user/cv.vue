<template>
    <div class="grid grid-cols-1 gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Currículum</h1>
                <p class="text-sm text-gray-500 mt-1">Gestiona tu CV, experiencias y formación</p>
            </div>
            <router-link to="/app/profile">
                <Button label="Volver al perfil" icon="pi pi-arrow-left" outlined severity="secondary" size="small" />
            </router-link>
        </div>

        <!-- CV Básico -->
        <Card>
            <template #title>Información general del CV</template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 flex flex-col gap-1">
                        <label class="text-sm font-medium">Título del CV</label>
                        <InputText v-model="cvForm.title" placeholder="Ej: Desarrollador/a Web Junior" class="w-full" />
                    </div>
                    <div class="md:col-span-2 flex flex-col gap-1">
                        <label class="text-sm font-medium">Resumen profesional</label>
                        <Textarea v-model="cvForm.summary" rows="4"
                            placeholder="Breve descripción sobre ti, tus objetivos y lo que aportas..." class="w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Habilidades</label>
                        <Textarea v-model="cvForm.skills" rows="3"
                            placeholder="Ej: JavaScript, trabajo en equipo, gestión del tiempo..." class="w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Idiomas</label>
                        <Textarea v-model="cvForm.languages" rows="3"
                            placeholder="Ej: Español (nativo), Inglés (B2), Francés (A2)..." class="w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Disponibilidad</label>
                        <Select v-model="cvForm.availability" :options="availabilityOptions"
                            option-label="label" option-value="value"
                            placeholder="¿Cuándo puedes incorporarte?" :show-clear="true" class="w-full" />
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <Button label="Guardar CV" icon="pi pi-check" :loading="isLoading" @click="handleSaveCv" size="small" />
                </div>
            </template>
        </Card>

        <!-- Experiencia Laboral -->
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Experiencia laboral</span>
                    <Button label="Añadir experiencia" icon="pi pi-plus" size="small" severity="primary" @click="openExpDialog()" />
                </div>
            </template>
            <template #content>
                <div v-if="cv?.experiences?.length" class="flex flex-col gap-3">
                    <div v-for="exp in cv.experiences" :key="exp.id"
                        class="border border-gray-200 dark:border-surface-700 rounded-lg p-4 flex items-start justify-between gap-4">
                        <div class="flex gap-3 items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                <i class="pi pi-briefcase text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ exp.position }}</p>
                                <p class="text-sm text-blue-600 font-medium">{{ exp.company }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ formatDate(exp.start_date) }} — {{ exp.end_date ? formatDate(exp.end_date) : 'Actualidad' }}
                                </p>
                                <p v-if="exp.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ exp.description }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <Button icon="pi pi-pencil" rounded text severity="secondary" size="small" @click="openExpDialog(exp)" v-tooltip.top="'Editar'" />
                            <Button icon="pi pi-trash" rounded text severity="danger" size="small" @click="handleDeleteExp(exp.id)" v-tooltip.top="'Eliminar'" />
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-400">
                    <i class="pi pi-briefcase text-3xl mb-2 block"></i>
                    <p class="text-sm">No has añadido experiencias todavía</p>
                </div>
            </template>
        </Card>

        <!-- Formación -->
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Formación académica</span>
                    <Button label="Añadir formación" icon="pi pi-plus" size="small" severity="primary" @click="openEduDialog()" />
                </div>
            </template>
            <template #content>
                <div v-if="cv?.educations?.length" class="flex flex-col gap-3">
                    <div v-for="edu in cv.educations" :key="edu.id"
                        class="border border-gray-200 dark:border-surface-700 rounded-lg p-4 flex items-start justify-between gap-4">
                        <div class="flex gap-3 items-start">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <i class="pi pi-graduation-cap text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ edu.degree }}</p>
                                <p class="text-sm text-green-600 font-medium">{{ edu.institution }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ formatDate(edu.start_date) }} — {{ edu.end_date ? formatDate(edu.end_date) : 'En curso' }}
                                </p>
                                <p v-if="edu.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ edu.description }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <Button icon="pi pi-pencil" rounded text severity="secondary" size="small" @click="openEduDialog(edu)" v-tooltip.top="'Editar'" />
                            <Button icon="pi pi-trash" rounded text severity="danger" size="small" @click="handleDeleteEdu(edu.id)" v-tooltip.top="'Eliminar'" />
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-400">
                    <i class="pi pi-graduation-cap text-3xl mb-2 block"></i>
                    <p class="text-sm">No has añadido formación todavía</p>
                </div>
            </template>
        </Card>

        <!-- Dialog Experiencia -->
        <Dialog v-model:visible="expDialogVisible" :header="expEditMode ? 'Editar experiencia' : 'Nueva experiencia'"
            :style="{ width: '520px' }" :modal="true">
            <div class="flex flex-col gap-4 pt-2">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Cargo / Puesto <span class="text-red-500">*</span></label>
                    <InputText v-model="expForm.position" placeholder="Ej: Desarrollador/a Frontend" class="w-full" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Empresa <span class="text-red-500">*</span></label>
                    <InputText v-model="expForm.company" placeholder="Nombre de la empresa" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Fecha inicio</label>
                        <InputText v-model="expForm.start_date" type="date" class="w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Fecha fin</label>
                        <InputText v-model="expForm.end_date" type="date" class="w-full" placeholder="Dejar vacío si es actual" />
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Descripción</label>
                    <Textarea v-model="expForm.description" rows="3"
                        placeholder="Describe tus responsabilidades y logros..." class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" text severity="secondary" @click="expDialogVisible = false" />
                <Button :label="expEditMode ? 'Guardar' : 'Añadir'" icon="pi pi-check"
                    :loading="isLoading" @click="handleSaveExp" />
            </template>
        </Dialog>

        <!-- Dialog Formación -->
        <Dialog v-model:visible="eduDialogVisible" :header="eduEditMode ? 'Editar formación' : 'Nueva formación'"
            :style="{ width: '520px' }" :modal="true">
            <div class="flex flex-col gap-4 pt-2">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Título / Grado <span class="text-red-500">*</span></label>
                    <InputText v-model="eduForm.degree" placeholder="Ej: Grado en Informática" class="w-full" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Centro / Institución <span class="text-red-500">*</span></label>
                    <InputText v-model="eduForm.institution" placeholder="Nombre del centro educativo" class="w-full" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Fecha inicio</label>
                        <InputText v-model="eduForm.start_date" type="date" class="w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">Fecha fin</label>
                        <InputText v-model="eduForm.end_date" type="date" class="w-full" placeholder="Dejar vacío si está en curso" />
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Descripción</label>
                    <Textarea v-model="eduForm.description" rows="3"
                        placeholder="Descripción opcional..." class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" text severity="secondary" @click="eduDialogVisible = false" />
                <Button :label="eduEditMode ? 'Guardar' : 'Añadir'" icon="pi pi-check"
                    :loading="isLoading" @click="handleSaveEdu" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import useCandidateCv from '@/composables/useCandidateCv'

const { cv, isLoading, getCv, saveCv, addExperience, updateExperience, deleteExperience, addEducation, updateEducation, deleteEducation } = useCandidateCv()

const availabilityOptions = [
    { label: 'Jornada completa', value: 'FULL_TIME' },
    { label: 'Media jornada', value: 'PART_TIME' },
    { label: 'Solo remoto', value: 'REMOTE_ONLY' },
    { label: 'Híbrido', value: 'HYBRID' },
    { label: 'Horario flexible', value: 'FLEX' },
]

const cvForm = ref({ title: 'Mi CV', summary: '', skills: '', languages: '', availability: null })

const expDialogVisible = ref(false)
const expEditMode = ref(false)
const expForm = ref(emptyExp())
const currentExpId = ref(null)

const eduDialogVisible = ref(false)
const eduEditMode = ref(false)
const eduForm = ref(emptyEdu())
const currentEduId = ref(null)

function emptyExp() { return { position: '', company: '', start_date: '', end_date: '', description: '' } }
function emptyEdu() { return { degree: '', institution: '', start_date: '', end_date: '', description: '' } }

const formatDate = (d) => {
    if (!d) return '—'
    const date = new Date(d)
    return date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' })
}

const syncCvForm = () => {
    if (cv.value) {
        cvForm.value = {
            title: cv.value.title ?? 'Mi CV',
            summary: cv.value.summary ?? '',
            skills: cv.value.skills ?? '',
            languages: cv.value.languages ?? '',
            availability: cv.value.availability ?? null,
        }
    }
}

const handleSaveCv = async () => {
    await saveCv(cvForm.value)
}

/* Experiencias */
const openExpDialog = (exp = null) => {
    if (exp) {
        expEditMode.value = true
        currentExpId.value = exp.id
        expForm.value = { position: exp.position, company: exp.company, start_date: exp.start_date ?? '', end_date: exp.end_date ?? '', description: exp.description ?? '' }
    } else {
        expEditMode.value = false
        currentExpId.value = null
        expForm.value = emptyExp()
    }
    expDialogVisible.value = true
}

const handleSaveExp = async () => {
    if (!expForm.value.position || !expForm.value.company) return
    let result
    if (expEditMode.value) {
        result = await updateExperience(currentExpId.value, expForm.value)
    } else {
        result = await addExperience(expForm.value)
    }
    if (result) expDialogVisible.value = false
}

const handleDeleteExp = async (id) => {
    await deleteExperience(id)
}

/* Formación */
const openEduDialog = (edu = null) => {
    if (edu) {
        eduEditMode.value = true
        currentEduId.value = edu.id
        eduForm.value = { degree: edu.degree, institution: edu.institution, start_date: edu.start_date ?? '', end_date: edu.end_date ?? '', description: edu.description ?? '' }
    } else {
        eduEditMode.value = false
        currentEduId.value = null
        eduForm.value = emptyEdu()
    }
    eduDialogVisible.value = true
}

const handleSaveEdu = async () => {
    if (!eduForm.value.degree || !eduForm.value.institution) return
    let result
    if (eduEditMode.value) {
        result = await updateEducation(currentEduId.value, eduForm.value)
    } else {
        result = await addEducation(eduForm.value)
    }
    if (result) eduDialogVisible.value = false
}

const handleDeleteEdu = async (id) => {
    await deleteEducation(id)
}

onMounted(async () => {
    await getCv()
    syncCvForm()
})
</script>
