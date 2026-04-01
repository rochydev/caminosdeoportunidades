import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useJobApplications() {
    const applications = ref([])
    const application = ref(null)
    const isLoading = ref(false)
    const toast = useToast()

    const getApplicationsForOffers = async (offerIds = []) => {
        if (!offerIds.length) {
            applications.value = []
            return []
        }
        isLoading.value = true
        try {
            const results = await Promise.all(
                offerIds.map(id =>
                    axios.get(`/api/job-applications?offer_id=${id}&per_page=100`)
                        .then(r => r.data?.data ?? [])
                )
            )
            applications.value = results.flat()
            return applications.value
        } catch {
            toast.error('Error', 'No se pudieron cargar las candidaturas')
            return []
        } finally {
            isLoading.value = false
        }
    }

    const updateApplication = async (id, payload) => {
        isLoading.value = true
        try {
            const response = await axios.put(`/api/job-applications/${id}`, payload)
            const data = response.data?.data ?? response.data
            toast.crud.updated('Candidatura')
            return data
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo actualizar la candidatura')
            return null
        } finally {
            isLoading.value = false
        }
    }

    return {
        applications,
        application,
        isLoading,
        getApplicationsForOffers,
        updateApplication
    }
}
