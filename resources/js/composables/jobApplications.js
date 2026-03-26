import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useJobApplications() {
  const myApplications = ref([])
  const recommendedOffers = ref([])
  const isLoadingApplications = ref(false)
  const isLoadingRecommendations = ref(false)
  const toast = useToast()

  const getMyApplications = async (userId) => {
    if (isLoadingApplications.value) return
    isLoadingApplications.value = true
    try {
      const response = await axios.get(`/api/job-applications/my-candidatures?candidate_id=${userId}`)
      myApplications.value = response.data?.data ?? []
      return response
    } catch (error) {
      console.error('Error en getMyApplications')
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
    } catch (error) {
      console.error('Error en getRecommendedOffers:'  )
      toast.error('Error', 'No se pudieron cargar las recomendaciones')
    } finally {
      isLoadingRecommendations.value = false
    }
  }

  return {
    myApplications,
    recommendedOffers,
    isLoadingApplications,
    isLoadingRecommendations,
    getMyApplications,
    getRecommendedOffers
  }
}
