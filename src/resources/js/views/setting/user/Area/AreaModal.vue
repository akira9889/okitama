<script setup>
import { apiClient } from '@/services/API.js'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import Modal from '@/components/Modal.vue'

import { onMounted, ref, watch } from 'vue'

const emit = defineEmits(['update:modelValue', 'onSubmit'])

const form = ref({})
const prefectures = ref({})
const cities = ref({})
const errorMsg = ref({})

async function getPrefectures() {
  const { data } = await apiClient.get('/prefecture')
  const transformedData = data.map((item) => {
    return {
      key: item.id,
      text: item.name,
    }
  })
  prefectures.value = transformedData
}

function changePrefecture({ value }) {
  form.value.prefecture_id = value
}

function changeCity({ value, text }) {
  form.value.city_id = value
  form.value.city_name = text
}

watch(
  () => form.value.prefecture_id,
  async (newPrefectureId, oldPrefectureId) => {
    form.value.city_id = ''

    if (newPrefectureId !== oldPrefectureId) {
      try {
        const { data } = await apiClient.get(`/cities`, { params: form.value })
        const transformedData = data.data.map((item) => {
          return {
            key: item.id,
            text: item.name,
          }
        })

        cities.value = transformedData
      } catch (error) {
        console.error('Error updating cities:', error)
      }
    }
  },
)

onMounted(() => {
  getPrefectures()
})

function closeModal() {
  emit('update:modelValue', false)
}

async function submit() {
  try {
    await apiClient.post('/area', form.value)
    closeModal()
    emit('onSubmit')
  } catch ({ response }) {
    errorMsg.value = response.data.errors
  }
}
</script>

<template>
  <Modal type="hideFooter" @click="closeModal">
    <div
      class="bg-white w-[90%] max-w-xl fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30"
    >
      <form @submit.prevent="submit">
        <div class="px-4 pt-5 pb-4">
          <InputError
            :error-msg="errorMsg?.database"
            class="mb-2 text-center"
          />

          <InputError :error-msg="errorMsg?.prefecture_id" class="mb-1" />
          <CustomInput
            v-model="form.prefecture_id"
            class="mb-4"
            label="都道府県"
            type="select"
            :select-options="prefectures"
            @change="changePrefecture"
          />
          <InputError :error-msg="errorMsg?.city_id" class="mb-1" />
          <CustomInput
            v-model="form.city_id"
            class="mb-4"
            label="市区町村"
            type="select"
            :select-options="cities"
            @change="changeCity"
          />
          <InputError :error-msg="errorMsg?.town_name" class="mb-1" />
          <CustomInput
            v-model="form.town_name"
            class="mb-4"
            label="町名"
            type="text"
          />
        </div>
        <footer class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="submit"
            class="mt-3 w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-customBlue hover:bg-customBlue"
          >
            登録
          </button>
        </footer>
      </form>
    </div>
  </Modal>
</template>
