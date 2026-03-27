import { ref, reactive } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useJobOffers() {
  const offers = ref([])
  const offer = ref(null)
  const catalogs = ref({ categories: [], contractTypes: [], workdayTypes: [], modalityTypes: [], disabilityTypes: [] })
  const isLoading = ref(false)
  const isSaving = ref(false)
  const pagination = ref({ total: 0, per_page: 15, current_page: 1, last_page: 1 })
  const toast = useToast()

  const offerForm = reactive({
    title: '',
    description: '',
    requirements: '',
    adaptations: '',
    city: '',
    category_id: null,
    contract_type_id: null,
    workday_type_id: null,
    modality_id: null,
    is_adapted: false,
    status: 'DRAFT',
  })

  const resetForm = () => {
    Object.assign(offerForm, {
      title: '', description: '', requirements: '', adaptations: '',
      city: '', category_id: null, contract_type_id: null,
      workday_type_id: null, modality_id: null, is_adapted: false, status: 'DRAFT',
    })
  }

  const fillForm = (data) => {
    Object.assign(offerForm, {
      title: data.title ?? '',
      description: data.description ?? '',
      requirements: data.requirements ?? '',
      adaptations: data.adaptations ?? '',
      city: data.city ?? '',
      category_id: data.category_id ?? null,
      contract_type_id: data.contract_type_id ?? null,
      workday_type_id: data.workday_type_id ?? null,
      modality_id: data.modality_id ?? null,
      is_adapted: data.is_adapted ?? false,
      status: data.status ?? 'DRAFT',
    })
  }

  const getCatalogs = async () => {
    try {
      const { data } = await axios.get('/api/catalogs/form-data')
      catalogs.value = data
    } catch {
      toast.error('Error', 'No se pudieron cargar los catálogos')
    }
  }

  const getOffers = async (params = {}) => {
    isLoading.value = true
    try {
      const { data } = await axios.get('/api/job-offers', { params })
      offers.value = data.data
      pagination.value = {
        total: data.total,
        per_page: data.per_page,
        current_page: data.current_page,
        last_page: data.last_page,
      }
    } catch {
      toast.error('Error', 'No se pudieron cargar las ofertas')
    } finally {
      isLoading.value = false
    }
  }

  const getOffer = async (id) => {
    isLoading.value = true
    try {
      const { data } = await axios.get(`/api/job-offers/${id}`)
      offer.value = data.data
      return data.data
    } catch {
      toast.error('Error', 'No se pudo cargar la oferta')
    } finally {
      isLoading.value = false
    }
  }

  const createOffer = async () => {
    isSaving.value = true
    try {
      const { data } = await axios.post('/api/job-offers', offerForm)
      toast.success('Oferta creada', 'La oferta se ha creado correctamente.')
      return data.data
    } catch (err) {
      const msg = err.response?.data?.message || 'No se pudo crear la oferta'
      toast.error('Error', msg)
      throw err
    } finally {
      isSaving.value = false
    }
  }

  const updateOffer = async (id) => {
    isSaving.value = true
    try {
      const { data } = await axios.put(`/api/job-offers/${id}`, offerForm)
      toast.success('Oferta actualizada', 'Los cambios se han guardado correctamente.')
      return data.data
    } catch (err) {
      const msg = err.response?.data?.message || 'No se pudo actualizar la oferta'
      toast.error('Error', msg)
      throw err
    } finally {
      isSaving.value = false
    }
  }

  const updateOfferStatus = async (id, status) => {
    try {
      const { data } = await axios.put(`/api/job-offers/${id}`, { status })
      const labels = { PUBLISHED: 'publicada', CLOSED: 'cerrada', DRAFT: 'guardada como borrador' }
      toast.success('Estado actualizado', `La oferta ha sido ${labels[status] ?? status}.`)
      return data.data
    } catch {
      toast.error('Error', 'No se pudo cambiar el estado de la oferta')
    }
  }

  const deleteOffer = async (id) => {
    try {
      await axios.delete(`/api/job-offers/${id}`)
      toast.success('Oferta eliminada', 'La oferta ha sido eliminada correctamente.')
      return true
    } catch {
      toast.error('Error', 'No se pudo eliminar la oferta')
      return false
    }
  }

  return {
    offers, offer, offerForm, catalogs,
    isLoading, isSaving, pagination,
    getCatalogs, getOffers, getOffer,
    createOffer, updateOffer, updateOfferStatus, deleteOffer,
    resetForm, fillForm,
  }
}
