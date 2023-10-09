import { createStore } from 'vuex'

import * as auth from './modules/Auth'
import * as toast from './modules/Toast'

const store = createStore({
  modules: {
    auth,
    toast,
  },
})

export default store
