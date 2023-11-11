export default function approved({ next, store}) {
  if (store.getters['auth/isApproved']) {
    next()
  } else {
    next({ name: 'not-approved' })
  }
}
