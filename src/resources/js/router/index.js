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
import Area from '@/views/setting/user/Area/Area.vue'
import DeliveryArea from '@/views/setting/user/DeliveryArea.vue'
import DropoffHistory from '@/views/dropoff/DropoffHistory.vue'
import ShowDropoffHistory from '@/views/dropoff/ShowDropoffHistory.vue'
import NotFound from '@/views/NotFound.vue'
import store from '@/store'
import auth from '../middleware/auth'
import guest from '../middleware/guest'
import admin from '../middleware/admin'
import middlewarePipeline from '@/router/middlewarePipeline'

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: '/login',
    component: AppLayout,
    meta: { middleware: [auth] },
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
        meta: { middleware: [admin] },
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
      {
        path: 'dropoff-history',
        name: 'dropoff-history',
        component: DropoffHistory,
      },
      {
        path: 'dropoff-history/:id',
        name: 'dropoff-history.show',
        component: ShowDropoffHistory,
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { middleware: [guest] },
  },
  {
    path: '/signup',
    name: 'signup',
    component: Signup,
    meta: { middleware: [guest] },
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
    meta: { middleware: [guest] },
  },
  {
    path: '/reset-password',
    name: 'resetPassword',
    component: ResetPassword,
    meta: { middleware: [guest] },
  },
  {
    path: '/:pathMatch(.*)',
    redirect: '/notfound',
  },
  {
    path: '/notfound',
    name: 'notfound',
    component: NotFound,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const middleware = to.meta.middleware
  const context = { to, from, next, store }

  if (!middleware) {
    return next()
  }

  middleware[0]({
    ...context,
    next: middlewarePipeline(context, middleware, 1),
  })
})

export default router
