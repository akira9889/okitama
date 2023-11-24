export const namespaced = true

export const state = {
  toasts: [],
}

export const mutations = {
  ADD_TOAST(state, toast) {
    state.toasts.push(toast)
    if (state.toasts.length > 3) {
      state.toasts.splice(0, 1)
    }
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
