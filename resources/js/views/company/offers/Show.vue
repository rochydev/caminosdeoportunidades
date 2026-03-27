<template>
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <Button
                icon="pi pi-arrow-left"
                text
                rounded
                severity="secondary"
                @click="router.push({ name: 'company.offers.index' })"
            />
            <div class="flex-1">
                <div v-if="offer" class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ offer.title }}</h1>
                        <span :class="statusBadge(offer.status)" class="text-xs font-medium px-2 py-1 rounded mt-1 inline-block">
                            {{ statusLabel(offer.status) }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <Button
                            v-if="offer.status === 'DRAFT'"
                            label="Publicar"
                            icon="pi pi-send"
                            severity="success"
                            @click="handleStatusChange('PUBLISHED')"
                        />
                        <Button
                            v-if="offer.status === 'PUBLISHED'"
                            label="Cerrar oferta"
                            icon="pi pi-lock"
                            severity="warn"
                            @click="handleStatusChange('CLOSED')"
                        />
                        <Button
                            label="Editar"
                            icon="pi pi-pencil"
                            outlined
                            @click="router.push({ name: 'company.offers.edit', params: { id: route.params.id } })"
                            :style="{ color: '#013C7B', borderColor: '#013C7B' }"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isLoading" class="space-y-4">
            <Skeleton height="120px" class="rounded-xl" />
            <Skeleton height="400px" class="rounded-xl" />
        </div>

        <div v-else-if="offer" class="space-y-6">
            <!-- Offer summary -->
            <Card class="rounded-xl border border-gray-200 shadow-sm">
                <template #content>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-[#013C7B]">{{ applicationCounts.total }}</p>
                            <p class="text-xs text-gray-500 mt-1">Candidaturas totales</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-600">{{ applicationCounts.sent }}</p>
                            <p class="text-xs text-gray-500 mt-1">Nuevas</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-yellow-600">{{ applicationCounts.inReview }}</p>
                            <p class="text-xs text-gray-500 mt-1">En revisión</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600">{{ applicationCounts.accepted }}</p>
                            <p class="text-xs text-gray-500 mt-1">Aceptadas</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Applications -->
            <Card class="rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <template #title>
                    <div class="flex justify-between items-center">
                        <span>Candidaturas recibidas</span>
                        <Select
                            v-model="statusFilter"
                            :options="filterOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos los estados"
                            class="w-44"
                            showClear
                        />
                    </div>
                </template>
                <template #content>
                    <!-- Loading -->
                    <div v-if="isLoadingApplications" class="py-8 text-center text-gray-400">
                        <i class="pi pi-spin pi-spinner text-2xl"></i>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="filteredApplications.length === 0" class="text-center py-12 text-gray-500">
                        <i class="pi pi-inbox text-4xl mb-3 block opacity-30"></i>
                        <p>No hay candidaturas{{ statusFilter ? ' con este estado' : '' }}</p>
                    </div>

                    <!-- List -->
                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="app in filteredApplications"
                            :key="app.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex flex-col sm:flex-row gap-4 items-start">
                                <!-- Avatar -->
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                                    <img
                                        v-if="app.candidate?.avatar"
                                        :src="app.candidate.avatar"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center bg-blue-50">
                                        <i class="pi pi-user text-[#013C7B] opacity-40"></i>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-gray-900">
                                            {{ app.candidate?.name }} {{ app.candidate?.surname1 }}
                                        </p>
                                        <span :class="appStatusBadge(app.status)" class="text-xs font-medium px-2 py-0.5 rounded">
                                            {{ appStatusLabel(app.status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ app.candidate?.email }}</p>
                                    <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-400">
                                        <span v-if="app.candidate?.profile?.city">
                                            <i class="pi pi-map-marker mr-1"></i>{{ app.candidate.profile.city }}
                                        </span>
                                        <span v-if="app.candidate?.profile?.disability_degree">
                                            Discapacidad: {{ app.candidate.profile.disability_degree }}%
                                        </span>
                                        <span>Recibida {{ formatDate(app.created_at) }}</span>
                                    </div>

                                    <!-- Notes -->
                                    <div v-if="app.company_notes" class="mt-2 p-2 bg-yellow-50 rounded text-xs text-yellow-800">
                                        <i class="pi pi-comment mr-1"></i>{{ app.company_notes }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2 items-end">
                                    <div class="flex gap-1">
                                        <!-- CV button -->
                                        <a
                                            v-if="app.candidate?.cv"
                                            :href="app.candidate.cv.url"
                                            target="_blank"
                                            class="p-button p-button-text p-button-rounded p-button-sm"
                                            v-tooltip.top="'Ver CV'"
                                        >
                                            <i class="pi pi-file-pdf text-red-500"></i>
                                        </a>

                                        <!-- Notes button -->
                                        <Button
                                            icon="pi pi-comment"
                                            text
                                            rounded
                                            size="small"
                                            severity="secondary"
                                            v-tooltip.top="'Añadir nota'"
                                            @click="openNotesDialog(app)"
                                        />
                                    </div>

                                    <!-- Status change -->
                                    <div class="flex gap-1 flex-wrap justify-end">
                                        <Button
                                            v-if="app.status === 'SENT'"
                                            label="Revisar"
                                            size="small"
                                            severity="warn"
                                            outlined
                                            @click="changeAppStatus(app, 'IN_REVIEW')"
                                        />
                                        <Button
                                            v-if="['SENT','IN_REVIEW'].includes(app.status)"
                                            label="Aceptar"
                                            size="small"
                                            severity="success"
                                            outlined
                                            @click="changeAppStatus(app, 'ACCEPTED')"
                                        />
                                        <Button
                                            v-if="['SENT','IN_REVIEW'].includes(app.status)"
                                            label="Rechazar"
                                            size="small"
                                            severity="danger"
                                            outlined
                                            @click="changeAppStatus(app, 'REJECTED')"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Notes Dialog -->
        <Dialog v-model:visible="notesDialog" header="Nota interna" modal :style="{ width: '440px' }">
            <div class="space-y-3">
                <p class="text-sm text-gray-600">
                    Candidatura de <strong>{{ selectedApp?.candidate?.name }} {{ selectedApp?.candidate?.surname1 }}</strong>
                </p>
                <Textarea
                    v-model="notesText"
                    rows="4"
                    placeholder="Añade tus notas sobre este candidato (solo visibles para tu empresa)..."
                    class="w-full"
                    autoResize
                />
            </div>
            <template #footer>
                <Button label="Cancelar" text severity="secondary" @click="notesDialog = false" />
                <Button label="Guardar nota" icon="pi pi-save" @click="saveNotes" :style="{ background: '#013C7B', borderColor: '#013C7B' }" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import useJobOffers from '@/composables/useJobOffers'
import useJobApplications from '@/composables/jobApplications'

const router = useRouter()
const route = useRoute()
const { offer, isLoading, getOffer, updateOfferStatus } = useJobOffers()
const { offerApplications, isLoadingOfferApplications, getOfferApplications, updateApplicationStatus } = useJobApplications()

const statusFilter = ref(null)
const notesDialog = ref(false)
const selectedApp = ref(null)
const notesText = ref('')

const filterOptions = [
    { label: 'Nuevas', value: 'SENT' },
    { label: 'En revisión', value: 'IN_REVIEW' },
    { label: 'Aceptadas', value: 'ACCEPTED' },
    { label: 'Rechazadas', value: 'REJECTED' },
]

onMounted(async () => {
    await getOffer(route.params.id)
    await getOfferApplications(route.params.id)
})

const filteredApplications = computed(() => {
    if (!statusFilter.value) return offerApplications.value
    return offerApplications.value.filter(a => a.status === statusFilter.value)
})

const applicationCounts = computed(() => ({
    total: offerApplications.value.length,
    sent: offerApplications.value.filter(a => a.status === 'SENT').length,
    inReview: offerApplications.value.filter(a => a.status === 'IN_REVIEW').length,
    accepted: offerApplications.value.filter(a => a.status === 'ACCEPTED').length,
}))

const handleStatusChange = async (status) => {
    await updateOfferStatus(route.params.id, status)
    await getOffer(route.params.id)
}

const changeAppStatus = async (app, status) => {
    await updateApplicationStatus(app.id, status)
    app.status = status
}

const openNotesDialog = (app) => {
    selectedApp.value = app
    notesText.value = app.company_notes ?? ''
    notesDialog.value = true
}

const saveNotes = async () => {
    await updateApplicationStatus(selectedApp.value.id, selectedApp.value.status, notesText.value)
    selectedApp.value.company_notes = notesText.value
    notesDialog.value = false
}

const statusLabel = (s) => ({ DRAFT: 'Borrador', PUBLISHED: 'Publicada', CLOSED: 'Cerrada' }[s] ?? s)
const statusBadge = (s) => ({
    DRAFT: 'bg-gray-100 text-gray-700',
    PUBLISHED: 'bg-green-100 text-green-700',
    CLOSED: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-700')

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
