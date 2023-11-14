<script setup>
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import GuestLayout from '@/components/GuestLayout.vue'
import AuthService from '@/services/AuthService'
import { ref } from 'vue'
import { getError } from '@/utils/helpers.js'

const form = ref({
  email: '',
})

const errors = ref({})

function forgotPassword() {
  AuthService.forgotPassword(form.value)
    .then(() => {
      //TODO メール送信通知
      console.log('成功')
    })
    .catch((error) => {
      errors.value = getError(error)
    })
}
</script>

<template>
  <GuestLayout title="新しいパスワードのリクエスト">
    <form class="space-y-6" @submit.prevent="forgotPassword">
      <div>
        <InputError :error-msg="errors?.email" class="mb-2" />
        <div class="mt-2">
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
          <router-link
            :to="{ name: 'login' }"
            class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
          >
            ログイン画面へ戻る
          </router-link>
        </div>
      </div>

      <div>
        <button
          type="submit"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          送信
        </button>
      </div>
    </form>
  </GuestLayout>
</template>
