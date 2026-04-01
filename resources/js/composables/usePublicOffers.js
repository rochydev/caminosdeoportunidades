import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function usePublicOffers() {
    const offers = ref({ data: [], meta: {}, links: {} })
    const offer = ref(null)
    const isLoading = ref(false)
    const toast = useToast()

    const getOffers = async (params = {}) => {
        isLoading.value = true
        try {
            const query = new URLSearchParams(
                Object.fromEntries(Object.entries(params).filter(([, v]) => v !== null && v !== '' && v !== undefined))
            ).toString()
            const response = await axios.get(`/api/public/job-offers${query ? '?' + query : ''}`)
            offers.value = response.data
            return response.data
        } catch {
            toast.error('Error', 'No se pudieron cargar las ofertas')
        } finally {
            isLoading.value = false
        }
    }

    const getOffer = async (id) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/api/public/job-offers/${id}`)
            offer.value = response.data?.data ?? null
            return offer.value
        } catch {
            toast.error('Error', 'No se encontró la oferta')
            return null
        } finally {
            isLoading.value = false
        }
    }

    return { offers, offer, isLoading, getOffers, getOffer }
}
