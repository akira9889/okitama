export const namespaced = true

export const state = {
  toasts: [],
}

export const mutations = {
  ADD_TOAST(state, toast) {
    state.toasts.push(toast)
  },
  REMOVE_TOAST(state, id) {
    state.toasts = state.toasts.filter((toast) => toast.id !== id)
  },
}

export const actions = {
  showToast({ commit }, toast) {
    toast.id = Date.now()
    commit('ADD_TOAST', toast)
  },
  hideToast({ commit }, id) {
    commit('REMOVE_TOAST', id)
  },
}
