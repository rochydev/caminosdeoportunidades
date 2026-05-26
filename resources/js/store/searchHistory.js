import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSearchHistoryStore = defineStore(
  'searchHistory',
  () => {
    // State
    const searches = ref([])
    const MAX_SEARCHES = 10

    // Actions
    const addSearch = (filters) => {
      // Crear objeto con solo los filtros activos
      const searchEntry = {
        id: Date.now(),
        search: filters.search || '',
        city: filters.city || '',
        category_id: filters.category_id || null,
        contract_type_id: filters.contract_type_id || null,
        modality_id: filters.modality_id || null,
        workday_type_id: filters.workday_type_id || null,
        is_adapted: filters.is_adapted || false,
        timestamp: new Date().toISOString(),
      }

      // Evitar duplicados: si la última búsqueda es igual, no agregar
      if (searches.value.length > 0) {
        const lastSearch = searches.value[0]
        if (
          lastSearch.search === searchEntry.search &&
          lastSearch.city === searchEntry.city &&
          lastSearch.category_id === searchEntry.category_id &&
          lastSearch.contract_type_id === searchEntry.contract_type_id &&
          lastSearch.modality_id === searchEntry.modality_id &&
          lastSearch.workday_type_id === searchEntry.workday_type_id &&
          lastSearch.is_adapted === searchEntry.is_adapted
        ) {
          return
        }
      }

      // Agregar al inicio del array
      searches.value.unshift(searchEntry)

      // Mantener solo los últimos 10
      if (searches.value.length > MAX_SEARCHES) {
        searches.value.pop()
      }
    }

    const clearHistory = () => {
      searches.value = []
    }

    return {
      searches,
      addSearch,
      clearHistory,
    }
  },
  {
    persist: true, // Usar pinia-plugin-persistedstate
  }
)