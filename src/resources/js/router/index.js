import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import Login from '@/views/auth/Login.vue'
import Signup from '@/views/auth/Signup.vue'
import RequestPassword from '@/views/auth/RequestPassword.vue'
import ResetPassword from '@/views/auth/ResetPassword.vue'
import SearchCustomer from '@/views/customer/SearchCustomer.vue'
import RegisterCustomer from '@/views/customer/RegisterCustomer.vue'
import EditCustomer from '@/views/customer/EditCustomer.vue'
import Users from '@/views/setting/admin/user/Users.vue'
import AwaitingUsers from '@/views/setting/admin/user/AwaitingUsers.vue'
import Area from '@/views/setting/user/Area.vue'
import DeliveryArea from '@/views/setting/user/DeliveryArea.vue'
import store from '@/store'
import AuthService from '@/services/AuthService'

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: '/search-customer',
    component: AppLayout,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: 'search-customer',
        name: 'search-customer',
        component: SearchCustomer,
      },
      {
        path: 'register-customer',
        name: 'register-customer',
        component: RegisterCustomer,
      },
      {
        path: 'edit-customer/:id',
        name: 'edit-customer',
        component: EditCustomer,
      },
      {
        path: 'users',
        name: 'users',
        component: Users,
      },
      {
        path: 'awaiting-users',
        name: 'awaiting-users',
        component: AwaitingUsers,
      },
      {
        path: 'area',
        name: 'area',
        component: Area,
      },
      {
        path: 'delivery-area',
        name: 'delivery-area',
        component: DeliveryArea,
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/signup',
    name: 'signup',
    component: Signup,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
    meta: { requiresGuest: true },
  },
  {
    path: '/reset-password',
    name: 'resetPassword',
    component: ResetPassword,
    meta: { requiresGuest: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

async function checkAuthStatus() {
  return await AuthService.checkAuthStatus()
}

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth && !store.state.auth.isLoggedIn) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && store.state.auth.isLoggedIn) {
    const isLoggedIn = await checkAuthStatus()

    if (isLoggedIn) {
      next({ name: 'search-customer' })
    } else {
      store.commit('auth/setLoggedIn', false)
      next({ name: 'login' })
    }
  } else {
    next()
  }
})

export default router
