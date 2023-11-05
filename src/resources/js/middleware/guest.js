export default function guest({ next, store }) {
  const isGuest = JSON.parse(window.localStorage.getItem('isGuest'))

  if (!isGuest && !store.getters['auth/authUser']) {
    store.dispatch('auth/getAuthUser').then(() => {
      if (store.getters['auth/authUser']) {
        store.dispatch('auth/setIsGuest', { value: false })
        next({ name: 'search-customer' })
      } else {
        store.dispatch('auth/setIsGuest', { value: true })
        next()
      }
    })
  } else {
    next()
  }
}
