import { ref } from 'vue'
import axios from 'axios'

export default function useAdminStats() {
    const stats = ref({})
    const isLoading = ref(false)

    const getStats = async () => {
        isLoading.value = true
        try {
            const response = await axios.get('/api/admin/stats')
            stats.value = response.data
            return response.data
        } catch (error) {
            console.error('Error loading admin stats:', error)
        } finally {
            isLoading.value = false
        }
    }

    return {
        stats,
        isLoading,
        getStats
    }
}
