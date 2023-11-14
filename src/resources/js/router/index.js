import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import Login from '@/views/auth/Login.vue'
import Signup from '@/views/auth/Signup.vue'
import RequestPassword from '@/views/auth/RequestPassword.vue'
import ResetPassword from '@/views/auth/ResetPassword.vue'
import NotApproved from '@/views/auth/NotApproved.vue'
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
import approved from '../middleware/approved'
import resetPassword from '../middleware/resetPassword'
import middlewarePipeline from '@/router/middlewarePipeline'

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: '/login',
    component: AppLayout,
    children: [
      {
        path: 'search-customer',
        name: 'search-customer',
        component: SearchCustomer,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'register-customer',
        name: 'register-customer',
        component: RegisterCustomer,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'edit-customer/:id',
        name: 'edit-customer',
        component: EditCustomer,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'users',
        name: 'users',
        component: Users,
        meta: { middleware: [auth, approved, admin] },
      },
      {
        path: 'awaiting-users',
        name: 'awaiting-users',
        component: AwaitingUsers,
        meta: { middleware: [auth, approved, admin] },
      },
      {
        path: 'area',
        name: 'area',
        component: Area,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'delivery-area',
        name: 'delivery-area',
        component: DeliveryArea,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'dropoff-history',
        name: 'dropoff-history',
        component: DropoffHistory,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'dropoff-history/:id',
        name: 'dropoff-history.show',
        component: ShowDropoffHistory,
        meta: { middleware: [auth, approved] },
      },
      {
        path: 'not-approved',
        name: 'not-approved',
        component: NotApproved,
        meta: { middleware: [auth] },
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
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
  },
  {
    path: '/reset-password',
    name: 'resetPassword',
    component: ResetPassword,
    meta: { middleware: [resetPassword]},
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
