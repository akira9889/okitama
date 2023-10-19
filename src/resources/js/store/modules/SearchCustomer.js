import { apiClient } from '@/services/API.js'

export const namespaced = true

export const state = {
  customers: [],
  prevSearch: '',
  showBackButton: false,
}

export const actions = {
  async getCustomers({ state, commit }, form = { search: state.prevSearch }) {
    if (!form.search) {
      commit('SET_SEARCH_FORM', form.search)
      commit('SET_CUSTOMERS', [])
      return
    }

    //テーブルリストから顧客詳細(1人)のコンポーネント(CustomerDetail)からgoBack関数を実行させるための条件式
    if (state.prevSearch === form.search && state.customers.length !== 1) {
      return
    }

    const { data } = await apiClient.get('/customer', { params: form })
    commit('SET_CUSTOMERS', data)

    if (data.length > 1) {
      commit('SET_SEARCH_FORM', form.search)
      commit('SET_SHOW_BACK_BUTTON', true)
    } else {
      commit('SET_SEARCH_FORM', form.search)
      commit('SET_SHOW_BACK_BUTTON', false)
    }
  },
}

export const mutations = {
  SET_CUSTOMERS(state, data) {
    state.customers = data
  },
  SET_SEARCH_FORM(state, query) {
    state.prevSearch = query
  },
  SET_SHOW_BACK_BUTTON(state, value) {
    state.showBackButton = value
  },
}
