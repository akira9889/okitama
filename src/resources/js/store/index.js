import { createStore } from 'vuex'

import * as auth from './modules/Auth'
import * as toast from './modules/Toast'
import * as searchCustomer from './modules/SearchCustomer'

const store = createStore({
  modules: {
    auth,
    toast,
    searchCustomer,
  },
})

export default store
