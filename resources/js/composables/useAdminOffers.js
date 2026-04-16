import { ref, inject } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useAdminOffers() {
    const offers = ref({ data: [] })
    const isLoading = ref(false)
    const toast = useToast()
    const swal = inject('$swal')

    const getOffers = async (params = {}) => {
        isLoading.value = true
        try {
            const query = new URLSearchParams(params).toString()
            const response = await axios.get(`/api/admin/offers?${query}`)
            offers.value = response.data
            return response
        } catch (error) {
            toast.error('Error', 'No se pudieron cargar las ofertas')
            throw error
        } finally {
            isLoading.value = false
        }
    }

    const updateOfferStatus = async (offerId, status) => {
        try {
            const response = await axios.put(`/api/admin/offers/${offerId}/status`, { status })
            if (offers.value.data) {
                const idx = offers.value.data.findIndex(o => o.id === offerId)
                if (idx !== -1) {
                    offers.value.data[idx].status = status
                }
            }
            toast.success('Estado actualizado', `La oferta ha sido marcada como ${status}`)
            return response.data
        } catch (error) {
            toast.error('Error', 'No se pudo actualizar el estado')
            throw error
        }
    }

    const deleteOffer = async (offerId) => {
        if (!swal) {
            if (!confirm('¿Estás seguro?')) return
            await axios.delete(`/api/admin/offers/${offerId}`)
            offers.value.data = offers.value.data.filter(o => o.id !== offerId)
            toast.crud.deleted('Oferta')
            return
        }

        const result = await swal({
            title: '¿Eliminar oferta?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            reverseButtons: true
        })

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/admin/offers/${offerId}`)
                offers.value.data = offers.value.data.filter(o => o.id !== offerId)
                toast.crud.deleted('Oferta')
            } catch (error) {
                toast.error('Error', 'No se pudo eliminar la oferta')
            }
        }
    }

    return {
        offers,
        isLoading,
        getOffers,
        updateOfferStatus,
        deleteOffer
    }
}
