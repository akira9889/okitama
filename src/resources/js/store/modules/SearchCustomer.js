import { apiClient } from '@/services/API.js'

export const namespaced = true

export const state = {
  customers: [],
  searchQuery: '',
  showBackButton: false,
}

export const actions = {
  async getCustomers({ state, commit }, form = state.searchQuery) {
    const { data } = await apiClient.get('/customer', { params: form })
    commit('SET_CUSTOMERS', data)
    if (data.length > 1) {
      commit('SET_SEARCH_QUERY', form)
      commit('SET_SHOW_BACK_BUTTON', true)
    } else {
      commit('SET_SHOW_BACK_BUTTON', false)
    }
  },
}

export const mutations = {
  SET_CUSTOMERS(state, data) {
    state.customers = data
  },
  SET_SEARCH_QUERY(state, query) {
    state.searchQuery = query
  },
  SET_SHOW_BACK_BUTTON(state, value) {
    state.showBackButton = value
  },
}
