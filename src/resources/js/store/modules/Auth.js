import router from '@/router'
import AuthService from '@/services/AuthService'
import { getError } from '@/utils/helpers'

export const namespaced = true

export const state = {
  user: null,
  loading: false,
  error: null,
}

export const actions = {
  async getAuthUser({ commit, dispatch }) {
    try {
      const { data } = await AuthService.getAuthUser()
      commit('setUser', data)
    } catch (error) {
      dispatch('logout')
    }
  },
  async registerUser({ commit }, form) {
    try {
      const { data } = await AuthService.registerUser(form)
      commit('setUser', data)
      router.push('/search-customer')
    } catch (error) {
      commit('setError', getError(error))
    }
  },
  async login({ commit, dispatch }, form) {
    try {
      const { data } = await AuthService.login(form)
      commit('setUser', data)
      dispatch('setIsGuest', { value: false })
    } catch (error) {
      commit('setUser', null)
      commit('setError', getError(error))
      dispatch('setIsGuest', { value: true })
    }
  },
  async logout({ commit, dispatch }) {
    try {
      await AuthService.logout()
      commit('setUser', null)
      dispatch('setIsGuest', { value: true })
      router.push('/login')
    } catch (error) {
      dispatch('setIsGuest', { value: true })
      commit('setError', error)
    }
  },
  setIsGuest(context, { value }) {
    window.localStorage.setItem('isGuest', value)
  },
}

export const mutations = {
  setUser(state, user) {
    state.user = user
  },
  setLoading(state, loading) {
    state.loading = loading
  },
  setError(state, error) {
    state.error = error
  },
}

export const getters = {
  authUser: (state) => {
    return state.user
  },
  error: (state) => {
    return state.error
  },
  loading: (state) => {
    return state.loading
  },
}
