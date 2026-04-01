import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useApplications() {
    const applications = ref([])
    const isLoading = ref(false)
    const toast = useToast()

    /** Candidaturas del usuario autenticado */
    const getMyApplications = async (userId) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/api/job-applications?candidate_user_id=${userId}&per_page=100`)
            applications.value = response.data?.data ?? []
            return applications.value
        } catch {
            toast.error('Error', 'No se pudieron cargar tus candidaturas')
            return []
        } finally {
            isLoading.value = false
        }
    }

    /** Comprueba si ya se ha solicitado una oferta concreta */
    const hasApplied = (offerId) => {
        return applications.value.some(a => a.offer_id === offerId || a.offer?.id === offerId)
    }

    const getApplicationForOffer = (offerId) => {
        return applications.value.find(a => a.offer_id === offerId || a.offer?.id === offerId) ?? null
    }

    /** Solicitar oferta */
    const apply = async (offerId, candidateUserId) => {
        isLoading.value = true
        try {
            const response = await axios.post('/api/job-applications', {
                offer_id: offerId,
                candidate_user_id: candidateUserId,
                status: 'SENT'
            })
            const app = response.data?.data ?? response.data
            applications.value.unshift(app)
            toast.success('¡Solicitud enviada!', 'Tu candidatura ha sido enviada correctamente')
            return app
        } catch (error) {
            if (error.response?.status === 422) {
                toast.warning('Ya solicitada', 'Ya has aplicado a esta oferta')
            } else {
                toast.error('Error', error.response?.data?.message ?? 'No se pudo enviar la solicitud')
            }
            return null
        } finally {
            isLoading.value = false
        }
    }

    /** Cancelar candidatura */
    const cancelApplication = async (id) => {
        isLoading.value = true
        try {
            await axios.put(`/api/job-applications/${id}`, { status: 'CANCELED' })
            const idx = applications.value.findIndex(a => a.id === id)
            if (idx !== -1) applications.value[idx].status = 'CANCELED'
            toast.success('Cancelada', 'Candidatura cancelada')
            return true
        } catch {
            toast.error('Error', 'No se pudo cancelar la candidatura')
            return false
        } finally {
            isLoading.value = false
        }
    }

    return { applications, isLoading, getMyApplications, hasApplied, getApplicationForOffer, apply, cancelApplication }
}
