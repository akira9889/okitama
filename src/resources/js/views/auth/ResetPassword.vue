<script setup>
import GuestLayout from '@/components/GuestLayout.vue'
import AuthService from '@/services/AuthService'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import { ref } from 'vue'
import { getError } from '@/utils/helpers.js'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'

const route = useRoute()
const param = route.query

const form = ref({
  password: '',
  password_confirmation: '',
  ...param,
})

const errors = ref({})

const router = useRouter()

function resetPassword() {
  AuthService.resetPassword(form.value)
    .then(() => {
      router.push({ name: 'login' })
    })
    .catch((error) => {
      errors.value = getError(error)
    })
}
</script>

<template>
  <GuestLayout title="新しいパスワードの設定">
    <form class="space-y-6" @submit.prevent="resetPassword">
      <div>
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
              required
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
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          送信
        </button>
      </div>
    </form>
  </GuestLayout>
</template>
