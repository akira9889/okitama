export default function auth({ next, store }) {
  if (!store.getters['auth/authUser']) {
    store.dispatch('auth/getAuthUser').then(() => {
      if (!store.getters['auth/authUser']) {
        store.dispatch('auth/setIsGuest', { value: true })
        next({ path: '/login' })
      } else {
        store.dispatch('auth/setIsGuest', { value: false })
        next()
      }
    })
  } else {
    next()
  }
}
