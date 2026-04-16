import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useAdminApplications() {
    const applications = ref({ data: [] })
    const isLoading = ref(false)
    const toast = useToast()

    const getApplications = async (params = {}) => {
        isLoading.value = true
        try {
            const query = new URLSearchParams(params).toString()
            const response = await axios.get(`/api/admin/applications?${query}`)
            applications.value = response.data
            return response
        } catch (error) {
            toast.error('Error', 'No se pudieron cargar las candidaturas')
            throw error
        } finally {
            isLoading.value = false
        }
    }

    const updateApplicationStatus = async (applicationId, status) => {
        try {
            const response = await axios.put(`/api/admin/applications/${applicationId}/status`, { status })
            if (applications.value.data) {
                const idx = applications.value.data.findIndex(a => a.id === applicationId)
                if (idx !== -1) {
                    applications.value.data[idx].status = status
                }
            }
            toast.success('Estado actualizado', `La candidatura ha sido marcada como ${status}`)
            return response.data
        } catch (error) {
            toast.error('Error', 'No se pudo actualizar el estado')
            throw error
        }
    }

    return {
        applications,
        isLoading,
        getApplications,
        updateApplicationStatus
    }
}
