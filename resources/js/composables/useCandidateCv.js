import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

export default function useCandidateCv() {
    const cv = ref(null)
    const isLoading = ref(false)
    const toast = useToast()

    const getCv = async () => {
        isLoading.value = true
        try {
            const response = await axios.get('/api/candidate-cv')
            cv.value = response.data?.data ?? null
            return cv.value
        } catch {
            toast.error('Error', 'No se pudo cargar el CV')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const saveCv = async (data) => {
        isLoading.value = true
        try {
            const response = await axios.post('/api/candidate-cv', data)
            cv.value = response.data?.data ?? response.data
            toast.crud.saved('CV')
            return cv.value
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo guardar el CV')
            return null
        } finally {
            isLoading.value = false
        }
    }

    /* ── Experiencias ── */
    const addExperience = async (data) => {
        isLoading.value = true
        try {
            const response = await axios.post('/api/cv-experiences', data)
            const exp = response.data?.data ?? response.data
            if (cv.value) cv.value.experiences = [exp, ...(cv.value.experiences ?? [])]
            toast.crud.created('Experiencia')
            return exp
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo añadir la experiencia')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const updateExperience = async (id, data) => {
        isLoading.value = true
        try {
            const response = await axios.put(`/api/cv-experiences/${id}`, data)
            const exp = response.data?.data ?? response.data
            if (cv.value?.experiences) {
                const idx = cv.value.experiences.findIndex(e => e.id === id)
                if (idx !== -1) cv.value.experiences[idx] = exp
            }
            toast.crud.updated('Experiencia')
            return exp
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo actualizar la experiencia')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const deleteExperience = async (id) => {
        isLoading.value = true
        try {
            await axios.delete(`/api/cv-experiences/${id}`)
            if (cv.value?.experiences) {
                cv.value.experiences = cv.value.experiences.filter(e => e.id !== id)
            }
            toast.crud.deleted('Experiencia')
            return true
        } catch {
            toast.error('Error', 'No se pudo eliminar la experiencia')
            return false
        } finally {
            isLoading.value = false
        }
    }

    /* ── Formación ── */
    const addEducation = async (data) => {
        isLoading.value = true
        try {
            const response = await axios.post('/api/cv-educations', data)
            const edu = response.data?.data ?? response.data
            if (cv.value) cv.value.educations = [edu, ...(cv.value.educations ?? [])]
            toast.crud.created('Formación')
            return edu
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo añadir la formación')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const updateEducation = async (id, data) => {
        isLoading.value = true
        try {
            const response = await axios.put(`/api/cv-educations/${id}`, data)
            const edu = response.data?.data ?? response.data
            if (cv.value?.educations) {
                const idx = cv.value.educations.findIndex(e => e.id === id)
                if (idx !== -1) cv.value.educations[idx] = edu
            }
            toast.crud.updated('Formación')
            return edu
        } catch (error) {
            toast.error('Error', error.response?.data?.message ?? 'No se pudo actualizar la formación')
            return null
        } finally {
            isLoading.value = false
        }
    }

    const deleteEducation = async (id) => {
        isLoading.value = true
        try {
            await axios.delete(`/api/cv-educations/${id}`)
            if (cv.value?.educations) {
                cv.value.educations = cv.value.educations.filter(e => e.id !== id)
            }
            toast.crud.deleted('Formación')
            return true
        } catch {
            toast.error('Error', 'No se pudo eliminar la formación')
            return false
        } finally {
            isLoading.value = false
        }
    }

    return {
        cv, isLoading,
        getCv, saveCv,
        addExperience, updateExperience, deleteExperience,
        addEducation, updateEducation, deleteEducation
    }
}
