import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useJobApplications() {
  const myApplications = ref([])
  const recommendedOffers = ref([])
  const offerApplications = ref([])
  const isLoadingApplications = ref(false)
  const isLoadingRecommendations = ref(false)
  const isLoadingOfferApplications = ref(false)
  const isApplying = ref(false)
  const toast = useToast()

  const getMyApplications = async (userId) => {
    if (isLoadingApplications.value) return
    isLoadingApplications.value = true
    try {
      const response = await axios.get(`/api/job-applications/my-candidatures?candidate_id=${userId}`)
      myApplications.value = response.data?.data ?? []
      return response
    } catch {
      toast.error('Error', 'No se pudieron cargar las inscripciones')
    } finally {
      isLoadingApplications.value = false
    }
  }

  const getRecommendedOffers = async (userId) => {
    if (isLoadingRecommendations.value) return
    isLoadingRecommendations.value = true
    try {
      const response = await axios.get(`/api/job-offers/recommended?candidate_id=${userId}`)
      recommendedOffers.value = response.data?.data ?? []
      return response
    } catch {
      toast.error('Error', 'No se pudieron cargar las recomendaciones')
    } finally {
      isLoadingRecommendations.value = false
    }
  }

  const applyToOffer = async (offerId, candidateUserId) => {
    if (isApplying.value) return
    isApplying.value = true
    try {
      const { data } = await axios.post('/api/job-applications', {
        offer_id: offerId,
        candidate_user_id: candidateUserId,
        status: 'SENT',
      })
      toast.success('¡Candidatura enviada!', 'Tu solicitud ha sido enviada correctamente.')
      return data.data
    } catch (err) {
      const msg = err.response?.data?.message || 'No se pudo enviar la candidatura'
      toast.error('Error', msg)
      throw err
    } finally {
      isApplying.value = false
    }
  }

  const getOfferApplications = async (offerId) => {
    isLoadingOfferApplications.value = true
    try {
      const { data } = await axios.get(`/api/job-applications/by-offer/${offerId}`)
      offerApplications.value = data.data ?? []
      return data
    } catch {
      toast.error('Error', 'No se pudieron cargar las candidaturas')
    } finally {
      isLoadingOfferApplications.value = false
    }
  }

  const updateApplicationStatus = async (applicationId, status, companyNotes = null) => {
    try {
      const payload = { status }
      if (companyNotes !== null) payload.company_notes = companyNotes
      const { data } = await axios.put(`/api/job-applications/${applicationId}`, payload)
      toast.success('Estado actualizado', 'La candidatura ha sido actualizada.')
      return data.data
    } catch {
      toast.error('Error', 'No se pudo actualizar el estado')
    }
  }

  return {
    myApplications,
    recommendedOffers,
    offerApplications,
    isLoadingApplications,
    isLoadingRecommendations,
    isLoadingOfferApplications,
    isApplying,
    getMyApplications,
    getRecommendedOffers,
    applyToOffer,
    getOfferApplications,
    updateApplicationStatus,
  }
}
