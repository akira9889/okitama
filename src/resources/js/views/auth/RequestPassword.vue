<script setup>
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import GuestLayout from '@/components/GuestLayout.vue'
import AuthService from '@/services/AuthService'
import { ref } from 'vue'
import { getError } from '@/utils/helpers.js'
import { useStore } from 'vuex'

const store = useStore()

const form = ref({
  email: '',
})

const errors = ref({})

function forgotPassword() {
  AuthService.forgotPassword(form.value)
    .then(({ data }) => {
      store.dispatch('toast/showToast', {
        message: data.message,
        delay: 5000,
      })
    })
    .catch((error) => {
      errors.value = getError(error)
    })
}
</script>

<template>
  <GuestLayout title="パスワードリセット">
    <p class="text-sm">
      ご登録のメールアドレスを入力し、リセットボタンを押してください。
    </p>
    <p class="text-sm mt-1">
      送信先メールにてパスワードリセット用のURLをお送りします。
    </p>
    <form class="space-y-6 mt-4" @submit.prevent="forgotPassword">
      <div>
        <InputError :error-msg="errors?.email" class="mb-2" />
        <div>
          <label
            for="email"
            class="block text-sm font-medium leading-6 text-gray-900 dark:text-black"
            >メールアドレス</label
          >
          <div class="mt-2">
            <CustomInput
              id="email"
              v-model="form.email"
              name="email"
              autocomplete="email"
            />
          </div>
        </div>
        <div class="my-8">
          <p class="text-sm text-gray-500">
            ログイン画面は
            <router-link
              :to="{ name: 'login' }"
              class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
            >
              こちら
            </router-link>
          </p>
        </div>
      </div>

      <div>
        <button
          type="submit"
          :disabled="!form.email"
          class="flex w-full justify-center rounded-md  px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-300"
          :class="form.email ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-indigo-200'"
        >
          リセット
        </button>
      </div>
    </form>
  </GuestLayout>
</template>
