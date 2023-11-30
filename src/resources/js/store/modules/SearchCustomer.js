import { apiClient } from '@/services/API.js'

export const namespaced = true

export const state = {
  customers: [],
  customerDetail: {},
  showBackButton: false,
  prevForm: {},
  loading: false,
}

function resetCustomerData(commit) {
  commit('SET_CUSTOMER_DETAIL', {})
  commit('SET_CUSTOMERS', [])
}

export const actions = {
  async getCustomers({ state, commit }, form) {
    const submitForm = { ...form }

    if (form.searchType === 'name') {
      if (!form.searchQuery) {
        resetCustomerData(commit)
        return
      }
      delete submitForm.town_id
      delete submitForm.searchAddress
    } else if (form.searchType === 'address') {
      if (!form.town_id) {
        resetCustomerData(commit)
        return
      }
      delete submitForm.searchQuery
    }

    if (JSON.stringify(submitForm) === JSON.stringify(state.prevForm)) {
      return
    }

    commit('SET_LOADING', true)

    commit('SET_PREV_FORM', submitForm)

    const { data } = await apiClient.get('/customer', { params: submitForm })

    if (data.length > 1) {
      commit('SET_CUSTOMER_DETAIL', {})
      commit('SET_CUSTOMERS', data)
    } else if (data.length === 1) {
      commit('SET_CUSTOMER_DETAIL', data[0])
    } else {
      resetCustomerData(commit)
    }

    commit('SET_LOADING', false)
  },
}

export const mutations = {
  SET_CUSTOMERS(state, data) {
    state.customers = data
  },
  SET_CUSTOMER_DETAIL(state, data) {
    state.customerDetail = data
  },
  SET_PREV_FORM(state, form) {
    state.prevForm = form
  },
  SET_SHOW_BACK_BUTTON(state, value) {
    state.showBackButton = value
  },
  SET_LOADING(state, value) {
    state.loading = value
  },
}
