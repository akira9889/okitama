export default function approved({ next, store }) {
  if (store.getters['auth/isApproved']) {
    next()
  } else {
    if (!store.getters['auth/authUser']) {
      next({ name: 'login' })
    } else {
      next({ name: 'not-approved' })
    }
  }
}
