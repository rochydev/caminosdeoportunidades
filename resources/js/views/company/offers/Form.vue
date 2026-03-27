<template>
    <div class="p-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <Button
                icon="pi pi-arrow-left"
                text
                rounded
                severity="secondary"
                @click="router.push({ name: 'company.offers.index' })"
            />
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar oferta' : 'Nueva oferta de empleo' }}</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ isEdit ? 'Modifica los datos de la oferta' : 'Rellena los datos para crear una nueva oferta' }}</p>
            </div>
        </div>

        <form @submit.prevent="handleSubmit">
            <div class="space-y-6">

                <!-- Basic info -->
                <Card class="rounded-xl border border-gray-200 shadow-sm">
                    <template #title>Información básica</template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">
                                    Título del puesto <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    v-model="offerForm.title"
                                    placeholder="Ej: Desarrollador/a Web Junior"
                                    class="w-full"
                                    :class="{ 'p-invalid': errors.title }"
                                />
                                <small v-if="errors.title" class="text-red-500">{{ errors.title }}</small>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">
                                    Descripción <span class="text-red-500">*</span>
                                </label>
                                <Textarea
                                    v-model="offerForm.description"
                                    rows="5"
                                    placeholder="Describe el puesto, las responsabilidades, el equipo..."
                                    class="w-full"
                                    :class="{ 'p-invalid': errors.description }"
                                    autoResize
                                />
                                <small v-if="errors.description" class="text-red-500">{{ errors.description }}</small>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Requisitos</label>
                                <Textarea
                                    v-model="offerForm.requirements"
                                    rows="4"
                                    placeholder="Formación, experiencia, habilidades técnicas requeridas..."
                                    class="w-full"
                                    autoResize
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Accessibility -->
                <Card class="rounded-xl border border-purple-200 shadow-sm">
                    <template #title>
                        <span class="flex items-center gap-2">
                            <i class="pi pi-heart text-purple-500"></i>
                            Accesibilidad e inclusión
                        </span>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg">
                                <Checkbox v-model="offerForm.is_adapted" :binary="true" inputId="is_adapted" />
                                <label for="is_adapted" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    Este puesto está adaptado para personas con discapacidad
                                </label>
                            </div>

                            <div v-if="offerForm.is_adapted">
                                <label class="text-sm font-medium text-gray-700 block mb-1">
                                    Describe las adaptaciones disponibles
                                </label>
                                <Textarea
                                    v-model="offerForm.adaptations"
                                    rows="3"
                                    placeholder="Ej: Puesto de trabajo adaptado, horario flexible, trabajo remoto disponible, software de accesibilidad..."
                                    class="w-full"
                                    autoResize
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Classification -->
                <Card class="rounded-xl border border-gray-200 shadow-sm">
                    <template #title>Clasificación y ubicación</template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Categoría</label>
                                <Select
                                    v-model="offerForm.category_id"
                                    :options="catalogs.categories"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Selecciona una categoría"
                                    class="w-full"
                                    showClear
                                />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Ciudad</label>
                                <InputText
                                    v-model="offerForm.city"
                                    placeholder="Ej: Madrid"
                                    class="w-full"
                                />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Tipo de contrato</label>
                                <Select
                                    v-model="offerForm.contract_type_id"
                                    :options="catalogs.contractTypes"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Selecciona tipo"
                                    class="w-full"
                                    showClear
                                />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Jornada</label>
                                <Select
                                    v-model="offerForm.workday_type_id"
                                    :options="catalogs.workdayTypes"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Selecciona jornada"
                                    class="w-full"
                                    showClear
                                />
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-1">Modalidad</label>
                                <Select
                                    v-model="offerForm.modality_id"
                                    :options="catalogs.modalityTypes"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Presencial / Remoto / Híbrido"
                                    class="w-full"
                                    showClear
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Status & actions -->
                <Card class="rounded-xl border border-gray-200 shadow-sm">
                    <template #title>Estado de la oferta</template>
                    <template #content>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Button
                                type="submit"
                                :label="isSaving ? 'Guardando...' : 'Guardar como borrador'"
                                icon="pi pi-save"
                                outlined
                                :loading="isSaving && offerForm.status === 'DRAFT'"
                                @click="offerForm.status = 'DRAFT'"
                                :style="{ color: '#013C7B', borderColor: '#013C7B' }"
                            />
                            <Button
                                type="submit"
                                label="Publicar oferta"
                                icon="pi pi-send"
                                :loading="isSaving && offerForm.status === 'PUBLISHED'"
                                @click="offerForm.status = 'PUBLISHED'"
                                :style="{ background: '#013C7B', borderColor: '#013C7B' }"
                            />
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            Los borradores son solo visibles para ti. Al publicar, la oferta estará disponible para los candidatos.
                        </p>
                    </template>
                </Card>

            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import useJobOffers from '@/composables/useJobOffers'

const router = useRouter()
const route = useRoute()
const { offerForm, catalogs, isSaving, getCatalogs, getOffer, createOffer, updateOffer, fillForm } = useJobOffers()

const isEdit = computed(() => !!route.params.id)
const errors = reactive({ title: '', description: '' })

onMounted(async () => {
    await getCatalogs()
    if (isEdit.value) {
        const data = await getOffer(route.params.id)
        if (data) fillForm(data)
    }
})

const validate = () => {
    errors.title = offerForm.title.trim() ? '' : 'El título es obligatorio'
    errors.description = offerForm.description.trim() ? '' : 'La descripción es obligatoria'
    return !errors.title && !errors.description
}

const handleSubmit = async () => {
    if (!validate()) return
    try {
        if (isEdit.value) {
            await updateOffer(route.params.id)
        } else {
            await createOffer()
        }
        router.push({ name: 'company.offers.index' })
    } catch {
        // error handled in composable
    }
}
</script>
