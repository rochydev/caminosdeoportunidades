import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useCandidateProfile() {
    const profile = ref(null)
    const isLoading = ref(false)
    const toast = useToast()

    const getProfile = async (userId) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/api/candidate-profiles/${userId}`)
            profile.value = response.data?.data ?? null
            return profile.value
        } catch (e) {
            if (e.response?.status !== 404) {
                toast.error('Error', 'No se pudo cargar el perfil')
            }
            profile.value = null
            return null
        } finally {
            isLoading.value = false
        }
    }

    const saveProfile = async (userId, data) => {
        isLoading.value = true
        try {
            let response
            if (profile.value) {
                response = await axios.put(`/api/candidate-profiles/${userId}`, data)
            } else {
                response = await axios.post('/api/candidate-profiles', { user_id: userId, ...data })
            }
            profile.value = response.data?.data ?? response.data
            toast.crud.saved('Perfil')
            return profile.value
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo guardar el perfil')
            return null
        } finally {
            isLoading.value = false
        }
    }

    return { profile, isLoading, getProfile, saveProfile }
}
