export default function admin({ next, store }) {
  if (store.getters['auth/isAdmin']) {
    next()
  } else {
    if (!store.getters['auth/authUser']) {
      next({ name: 'login' })
    } else {
      next({ name: 'notfound' })
    }
  }
}
