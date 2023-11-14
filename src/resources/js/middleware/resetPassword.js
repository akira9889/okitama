export default function resetPassword({ next, to }) {
  if (!to.query.email || !to.query.token) {
    next({ name: 'notfound' })
  } else {
    next()
  }
}
