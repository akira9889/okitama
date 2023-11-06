export default function guest({ next, store }) {
  // ユーザーの認証状態を取得する関数
  function handleAuthState() {
    if (store.getters['auth/authUser']) {
      // ユーザーが認証されている場合
      store.dispatch('auth/setIsGuest', { value: false })
      next({ name: 'search-customer' })
    } else {
      // ユーザーが認証されていない場合
      store.dispatch('auth/setIsGuest', { value: true })
      next()
    }
  }

  // ローカルストレージからゲスト状態を取得し、真偽値に変換する（存在しない場合はnullを返す）
  const isGuest = JSON.parse(window.localStorage.getItem('isGuest'))

  // ゲスト状態がfalse、またはauthUserが未確認の場合、ユーザーの認証状態を確認する
  if (!isGuest || !store.getters['auth/authUser']) {
    store.dispatch('auth/getAuthUser').then(handleAuthState)
  } else {
    // ゲスト状態がtrueでもauthUserが確認済みの場合、検索ページへリダイレクトする
    next({ name: 'search-customer' })
  }
}
