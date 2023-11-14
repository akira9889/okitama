<script setup>
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import { computed, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const router = useRouter()
const store = useStore()

const form = ref({
  email: '',
  password: '',
  remember: false,
})
const errors = computed(() => store.getters['auth/error'])

const login = async () => {
  await store.dispatch('auth/login', form.value)
  errors.value = null
  router.push({ name: 'search-customer' })
}

onUnmounted(() => {
  store.commit('auth/setError', null)
})
</script>

<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-8 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2
        class="mt-6 text-center text-3xl font-bold leading-9 tracking-tight text-gray-900"
      >
        アカウントログイン
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="login">
        <div>
          <InputError :error-msg="errors?.email" class="mb-2" />
          <label
            for="email"
            class="block text-sm font-medium leading-6 text-gray-900"
            >メールアドレス</label
          >
          <div class="mt-2">
            <CustomInput
              id="email"
              v-model="form.email"
              type="text"
              name="email"
              autocomplete="email"
            />
          </div>
        </div>

        <div>
          <InputError :error-msg="errors?.password" class="mb-2" />
          <label
            for="password"
            class="block text-sm font-medium leading-6 text-gray-900"
            >パスワード</label
          >
          <div class="mt-2">
            <CustomInput
              id="password"
              v-model="form.password"
              type="password"
              name="password"
              autocomplete="current-password"
            />
          </div>
          <div>
            <div class="mt-4 flex items-center">
              <input
                id="remember-me"
                v-model="form.remember"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-9000"
                >ログイン状態を保持する</label
              >
            </div>
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            ログイン
          </button>
        </div>
      </form>
    </div>
    <p class="mt-10 text-center text-sm text-gray-500">
      アカウントがない方は
      <router-link
        :to="{ name: 'signup' }"
        class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
      >
        こちら
      </router-link>
    </p>
    <hr class="mt-4" />
    <p class="mt-4 text-center text-sm text-gray-500">
      パスワードを忘れた方は
      <router-link
        :to="{ name: 'requestPassword' }"
        class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
      >
        こちら
      </router-link>
    </p>
  </div>
</template>
