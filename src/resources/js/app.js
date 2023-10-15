import './bootstrap'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faEye, faEyeSlash } from '@fortawesome/free-regular-svg-icons'
import { faChevronRight, faXmark } from '@fortawesome/free-solid-svg-icons'
/* add icons to the library */
library.add(faEye, faEyeSlash, faChevronRight, faXmark)

const app = createApp(App)

app
  .component('FontAwesomeIcon', FontAwesomeIcon)
  .use(store)
  .use(router)
  .mount('#app')
