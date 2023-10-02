<script setup>
import { computed, ref } from 'vue'
import store from '@/store'

const form = ref({
  email: '',
  first_name: '',
  last_name: '',
  password: '',
  password_confirmation: '',
})

const errors = computed(() => store.getters['auth/error'])

const showPassword = ref(false)

const signup = async () => {
  store.dispatch('auth/registerUser', form.value)
}
</script>

<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-8 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2
        class="mt-6 text-center text-3xl font-bold leading-9 tracking-tight text-gray-900"
      >
        アカウント作成
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="signup()">
        <div>
          <label
            for="email"
            class="block text-sm font-medium leading-6 text-gray-900"
            >メールアドレス</label
          >
          <div class="mt-2">
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="block w-full rounded-md border-0 p-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            />
            <template v-if="errors?.email">
              <div
                v-for="(error, index) in errors.email"
                :key="index"
                class="text-sm text-red-700 m-1"
                role="alert"
              >
                {{ error }}
              </div>
            </template>
          </div>
        </div>

        <div>
          <template v-if="errors?.last_name">
            <div
              v-for="(error, index) in errors.last_name"
              :key="index"
              class="text-sm text-red-700 m-1"
              role="alert"
            >
              {{ error }}
            </div>
          </template>
          <template v-if="errors?.first_name">
            <div
              v-for="(error, index) in errors.first_name"
              :key="index"
              class="text-sm text-red-700 m-1"
              role="alert"
            >
              {{ error }}
            </div>
          </template>
          <div class="mt-2 flex justify-between">
            <div>
              <label
                for="last_name"
                class="block text-sm font-medium leading-6 text-gray-900"
                >姓</label
              >
              <input
                id="last_name"
                v-model="form.last_name"
                type="text"
                required
                class="block w-full rounded-md border-0 p-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              />
            </div>
            <div>
              <label
                for="first_name"
                class="block text-sm font-medium leading-6 text-gray-900"
                >名</label
              >
              <input
                id="first_name"
                v-model="form.first_name"
                type="text"
                required
                class="block w-full rounded-md border-0 p-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              />
            </div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password"
              class="block text-sm font-medium leading-6 text-gray-900"
              >パスワード</label
            >
          </div>
          <div class="mt-2 relative">
            <input
              id="password"
              v-model="form.password"
              name="password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="block w-full rounded-md border-0 p-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            />
            <button
              v-show="form.password"
              type="button"
              class="absolute inset-y-0 right-0 px-3 flex items-center text-sm leading-5"
              @click="showPassword = !showPassword"
            >
              <font-awesome-icon
                v-if="showPassword"
                :icon="['far', 'eye-slash']"
              />
              <font-awesome-icon
                v-else-if="!showPassword"
                :icon="['far', 'eye']"
              />
            </button>
            <template v-if="errors?.password">
              <div
                v-for="(error, index) in errors.password"
                :key="index"
                class="text-sm text-red-700 m-1"
                role="alert"
              >
                {{ error }}
              </div>
            </template>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password"
              class="block text-sm font-medium leading-6 text-gray-900"
              >パスワード確認</label
            >
          </div>
          <div class="mt-2">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="block w-full rounded-md border-0 p-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            登録
          </button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm text-gray-500">
        すでにアカウントがある場合は
        <router-link
          to="/login"
          class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
        >
          こちら
        </router-link>
      </p>
    </div>
  </div>
</template>
