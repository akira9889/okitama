<script setup>
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import { computed, onUnmounted, ref } from 'vue'
import store from '@/store'

const form = ref({
  email: '',
  first_name: '',
  last_name: '',
  password: '',
  password_confirmation: '',
})

const errors = computed(() => store.getters['auth/error'])

const signup = async () => {
  store.dispatch('auth/registerUser', form.value)
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
        アカウント作成
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="signup()">
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
              name="email"
              required
              autocomplete="email"
              enterkeyhint="next"
            />
          </div>
        </div>

        <div>
          <InputError :error-msg="errors?.last_name" class="mb-2" />
          <InputError :error-msg="errors?.first_name" class="mb-2" />
          <div class="mt-2 flex justify-between">
            <div class="form-name">
              <label
                for="last_name"
                class="block text-sm font-medium leading-6 text-gray-900"
                >姓</label
              >
              <CustomInput
                id="last_name"
                v-model="form.last_name"
                name="last_name"
                required
                autocomplete="family-name"
                enterkeyhint="next"
              />
            </div>
            <div class="form-name">
              <label
                for="first_name"
                class="block text-sm font-medium leading-6 text-gray-900"
                >名</label
              >
              <CustomInput
                id="first_name"
                v-model="form.first_name"
                name="first_name"
                required
                autocomplete="given-name"
                enterkeyhint="next"
              />
            </div>
          </div>
        </div>

        <div>
          <InputError :error-msg="errors?.password" class="mb-2" />
          <div class="flex items-center justify-between">
            <label
              for="password"
              class="block text-sm font-medium leading-6 text-gray-900"
              >パスワード</label
            >
          </div>
          <div class="mt-2">
            <CustomInput
              id="password"
              v-model="form.password"
              type="password"
              name="password"
              required
              enterkeyhint="next"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password_confirmation"
              class="block text-sm font-medium leading-6 text-gray-900"
              >パスワード確認</label
            >
          </div>
          <div class="mt-2">
            <CustomInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              name="password_confirmation"
              required
              enterkeyhint="done"
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
<style scoped>
.form-name {
  width: calc(50% - 15px);
}
</style>
