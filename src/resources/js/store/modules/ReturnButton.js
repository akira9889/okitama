export const namespaced = true

export const state = {
  show: false,
  returnRoute: {
    name: '',
    params: {},
  },
}

export const mutations = {
  SHOW_BUTTON(state, boolean) {
    state.show = boolean
  },
  SET_ROUTE(state, { route, params }) {
    state.returnRoute.name = route
    state.returnRoute.params = params
  },
}

export const actions = {
  showButton({ commit }, boolean) {
    commit('SHOW_BUTTON', boolean)
  },
  setRoute({ commit }, { route, params = {} }) {
    commit('SET_ROUTE', { route, params })
  },
}
