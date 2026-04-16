import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useAdminCompanies() {
    const companies = ref({ data: [] })
    const company = ref(null)
    const isLoading = ref(false)
    const toast = useToast()

    const getCompanies = async (params = {}) => {
        isLoading.value = true
        try {
            const query = new URLSearchParams(params).toString()
            const response = await axios.get(`/api/admin/companies?${query}`)
            companies.value = response.data
            return response
        } catch (error) {
            toast.error('Error', 'No se pudieron cargar las empresas')
            throw error
        } finally {
            isLoading.value = false
        }
    }

    const getCompany = async (userId) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/api/admin/companies/${userId}`)
            company.value = response.data
            return response.data
        } catch (error) {
            toast.error('Error', 'No se pudo cargar la empresa')
            throw error
        } finally {
            isLoading.value = false
        }
    }

    const updateCompanyStatus = async (userId, status) => {
        try {
            const response = await axios.put(`/api/admin/companies/${userId}/status`, {
                validation_status: status
            })
            if (companies.value.data) {
                const idx = companies.value.data.findIndex(c => c.user_id === userId)
                if (idx !== -1) {
                    companies.value.data[idx].validation_status = status
                }
            }
            toast.success('Estado actualizado', `La empresa ha sido marcada como ${status}`)
            return response.data
        } catch (error) {
            toast.error('Error', 'No se pudo actualizar el estado')
            throw error
        }
    }

    return {
        companies,
        company,
        isLoading,
        getCompanies,
        getCompany,
        updateCompanyStatus
    }
}
