import axios from 'axios'
import store from '@/store'

export const apiClient = axios.create({
  baseURL: '/api',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

apiClient.interceptors.response.use(
  (response) => {
    return response
  },
  function (error) {
    if (
      error.response &&
      [401, 419].includes(error.response.status) &&
      store.getters['auth/authUser']
    ) {
      store.dispatch('auth/logout')
    }
    return Promise.reject(error)
  },
)
