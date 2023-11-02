import { createStore } from 'vuex'

import * as auth from './modules/Auth'
import * as toast from './modules/Toast'
import * as searchCustomer from './modules/SearchCustomer'
import * as returnButton from './modules/ReturnButton'

const store = createStore({
  modules: {
    auth,
    toast,
    searchCustomer,
    returnButton,
  },
})

export default store
