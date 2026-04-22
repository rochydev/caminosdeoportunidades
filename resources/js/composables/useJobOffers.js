import { ref, inject } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useJobOffers() {
    const offers = ref({ data: [], meta: {} })
    const offer = ref(initialOffer())
    const isLoading = ref(false)
    const toast = useToast()
    const swal = inject('$swal')

    function initialOffer() {
        return {
            id: null,
            company_user_id: null,
            category_id: null,
            contract_type_id: null,
            workday_type_id: null,
            modality_id: null,
            title: '',
            description: '',
            requirements: '',
            adaptations: '',
            city: '',
            is_adapted: false,
            status: 'DRAFT',
            image: null,
            image_url: null
        }
    }

    const resetOffer = () => { offer.value = initialOffer() }

    const setOffer = (data = {}) => {
        offer.value = {
            id: data.id ?? null,
            company_user_id: data.company_user_id ?? null,
            category_id: data.category?.id ?? data.category_id ?? null,
            contract_type_id: data.contract_type?.id ?? data.contract_type_id ?? null,
            workday_type_id: data.workday_type?.id ?? data.workday_type_id ?? null,
            modality_id: data.modality?.id ?? data.modality_id ?? null,
            title: data.title ?? '',
            description: data.description ?? '',
            requirements: data.requirements ?? '',
            adaptations: data.adaptations ?? '',
            city: data.city ?? '',
            is_adapted: data.is_adapted ?? false,
            status: data.status ?? 'DRAFT',
            image: null,
            image_url: data.image_url ?? null
        }
    }

    const buildFormData = (payload) => {
        const formData = new FormData()
        Object.entries(payload).forEach(([key, value]) => {
            if (key === 'image_url') return
            if (value === null || value === undefined || value === '') return
            if (key === 'image' && value instanceof File) {
                formData.append('image', value)
            } else if (key === 'is_adapted') {
                formData.append(key, value ? 1 : 0)
            } else {
                formData.append(key, value)
            }
        })
        return formData
    }

    const getOffers = async (params = {}) => {
        isLoading.value = true
        try {
            const query = new URLSearchParams({ per_page: 100, ...params }).toString()
            const response = await axios.get(`/api/job-offers?${query}`)
            offers.value = response.data
            return response.data
        } catch {
            toast.error('Error', 'No se pudieron cargar las ofertas')
        } finally {
            isLoading.value = false
        }
    }

    const createOffer = async () => {
        if (!offer.value.title || !offer.value.description) {
            toast.error('Error de validación', 'El título y la descripción son obligatorios')
            return null
        }
        isLoading.value = true
        try {
            const hasFile = offer.value.image instanceof File
            const config = hasFile ? { headers: { 'Content-Type': 'multipart/form-data' } } : {}
            const payload = hasFile ? buildFormData(offer.value) : offer.value
            const response = await axios.post('/api/job-offers', payload, config)
            const data = response.data?.data ?? response.data
            toast.crud.created('Oferta')
            return data
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo crear la oferta')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const updateOffer = async () => {
        if (!offer.value.title || !offer.value.description) {
            toast.error('Error de validación', 'El título y la descripción son obligatorios')
            return null
        }
        isLoading.value = true
        try {
            const hasFile = offer.value.image instanceof File
            let response
            if (hasFile) {
                // Con FormData Laravel no procesa PUT multipart, usamos POST con _method=PUT
                const formData = buildFormData(offer.value)
                formData.append('_method', 'PUT')
                response = await axios.post(`/api/job-offers/${offer.value.id}`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
            } else {
                response = await axios.put(`/api/job-offers/${offer.value.id}`, offer.value)
            }
            const data = response.data?.data ?? response.data
            toast.crud.updated('Oferta')
            return data
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo actualizar la oferta')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const uploadOfferImage = async (offerId, file) => {
        try {
            const formData = new FormData()
            formData.append('image', file)
            const response = await axios.post(`/api/job-offers/${offerId}/image`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            toast.success('Imagen actualizada', 'La imagen de la oferta se ha subido correctamente')
            return response.data?.data
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo subir la imagen')
            return null
        }
    }

    const deleteOfferImage = async (offerId) => {
        try {
            await axios.delete(`/api/job-offers/${offerId}/image`)
            toast.success('Imagen eliminada', 'La imagen de la oferta se ha eliminado')
            return true
        } catch {
            toast.error('Error', 'No se pudo eliminar la imagen')
            return false
        }
    }

    const deleteOffer = async (id) => {
        return new Promise((resolve) => {
            swal({
                title: '¿Eliminar oferta?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/api/job-offers/${id}`)
                        toast.crud.deleted('Oferta')
                        resolve(true)
                    } catch {
                        toast.error('Error', 'No se pudo eliminar la oferta')
                        resolve(false)
                    }
                } else {
                    resolve(false)
                }
            })
        })
    }

    return {
        offers,
        offer,
        isLoading,
        initialOffer,
        resetOffer,
        setOffer,
        getOffers,
        createOffer,
        updateOffer,
        uploadOfferImage,
        deleteOfferImage,
        deleteOffer
    }
}
