<template>
  <div class="rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer bg-white">
    <!-- Company Image -->
    <div class="h-32 overflow-hidden bg-gray-100">
      <img 
        v-if="job?.company?.avatar" 
        :src="job.company.avatar" 
        :alt="job.company.name" 
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
        <i class="pi pi-building text-gray-400 text-2xl"></i>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4 flex flex-col h-full">
      <!-- Title -->
      <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ job?.title }}</h3>

      <!-- Company Name -->
      <p class="text-sm text-gray-600 mb-3">{{ job?.company?.name }}</p>

      <!-- Status Badge -->
      <div class="mb-3">
        <span 
          v-if="job?.application_status" 
          :class="getApplicationStatusBadgeClass(job.application_status)"
          class="inline-block text-xs font-medium px-3 py-1 rounded"
        >
          {{ formatApplicationStatus(job.application_status) }}
        </span>
      </div>

      <!-- Salary -->
      <div v-if="job?.salary" class="mb-3 text-sm">
        <p class="text-gray-700 font-semibold">{{ job.salary }}</p>
      </div>

      <!-- Tags -->
      <div class="flex gap-2 mb-4 flex-wrap">
        <span v-if="job?.category?.name" class="text-xs bg-blue-50 text-[#013C7B] px-2 py-1 rounded font-medium">
          {{ job.category.name }}
        </span>
        <span v-if="job?.city" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
          {{ job.city }}
        </span>
      </div>

      <!-- Button / Status -->
      <div class="mt-auto pt-4 border-t border-gray-100">
        <button 
          v-if="!isApplied" 
          class="w-full p-button p-button-outlined text-sm"
          @click="$emit('apply')"
        >
          Solicitar
        </button>
        <div v-else class="text-sm text-gray-500 text-center py-2 font-medium">
          <i class="pi pi-check text-green-600 mr-2"></i> Solicitado
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  job: {
    type: Object,
    default: () => ({})
  },
  isApplied: {
    type: Boolean,
    default: false
  }
})

defineEmits(['apply'])

const formatStatus = (status) => {
  const statusMap = {
    'DRAFT': 'Borrador',
    'PUBLISHED': 'Publicada',
    'CLOSED': 'Cerrada'
  }
  return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
  const classMap = {
    'DRAFT': 'bg-gray-100 text-gray-700',
    'PUBLISHED': 'bg-green-100 text-green-700',
    'CLOSED': 'bg-red-100 text-red-700'
  }
  return classMap[status] || 'bg-gray-100 text-gray-700'
}

const formatApplicationStatus = (status) => {
  const statusMap = {
    'SENT': 'Enviada',
    'IN_REVIEW': 'En Revisión',
    'ACCEPTED': 'Aceptada',
    'REJECTED': 'Rechazada',
    'CANCELED': 'Cancelada'
  }
  return statusMap[status] || status
}

const getApplicationStatusBadgeClass = (status) => {
  const classMap = {
    'SENT': 'bg-blue-100 text-blue-700',
    'IN_REVIEW': 'bg-yellow-100 text-yellow-700',
    'ACCEPTED': 'bg-green-100 text-green-700',
    'REJECTED': 'bg-red-100 text-red-700',
    'CANCELED': 'bg-gray-100 text-gray-700'
  }
  return classMap[status] || 'bg-gray-100 text-gray-700'
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

:deep(.p-button-outlined) {
  color: #013C7B !important;
  border-color: #013C7B !important;
  width: 100%;
}

:deep(.p-button-outlined:hover) {
  background-color: #013C7B !important;
  color: white !important;
  border-color: #013C7B !important;
}
</style>
