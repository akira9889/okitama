<script setup>
import { apiClient } from '@/services/API.js'
import Btn from '@/components/Btn.vue'
import CustomInput from '@/components/CustomInput.vue'
import { computed,  onUnmounted, ref } from 'vue'
import { useStore } from 'vuex'

defineProps({ customer: Object })

const store = useStore()
const showBackButton = computed(() => store.state.searchCustomer.showBackButton)

onUnmounted(() => {
  store.commit('searchCustomer/SET_SHOW_BACK_BUTTON', false)
})

const goBack = () => {
  store.commit('searchCustomer/SET_CUSTOMER_DETAIL', {})
  store.commit('searchCustomer/SET_PREV_FORM', {})
}

const fileUrl = ref('')
const form = ref({})

function previewImage(file) {
  if (file) {
    form.value.file = file
    fileUrl.value = URL.createObjectURL(file)
  }
}

function submit() {
  const formData = new FormData()
  if (form.value.file instanceof File) {
    formData.append('file', form.value.file)
  }
  apiClient.post('/dropoff', formData)
}
</script>

<template>
  <div class="w-full">
    <div class="text-center">
      <h2 class="text-xl">顧客情報</h2>
      <p class="text-xl mt-6 inline-block">
        {{ customer.last_name }} {{ customer.first_name }}
      </p>
      <p class="mt-4">住所</p>
      <div class="flex w-full justify-center">
        <div>
          {{ customer.town_name + customer.address_number }}
        </div>
        &emsp;
        <div>{{ customer.room_number }}</div>
      </div>
    </div>

    <div v-if="customer.dropoffs.length" class="flex items-center">
      <div class="w-1/2 text-center">
        <p class="text-xl">置き配</p>
        <div class="bg-customGreen mt-4">
          <span class="text-[120px] font-thin text-teal-600">○</span>
        </div>
      </div>

      <div class="w-1/2 text-center">
        <p class="text-xl">場所</p>
        <p
          v-for="(dropoff, index) in customer.dropoffs"
          :key="index"
          class="text-l font-bold"
        >
          {{ dropoff.name }}
        </p>
      </div>
    </div>

    <div v-else class="w-1/2 text-center mx-auto">
      <p class="text-xl">置き配</p>
      <div class="bg-red-200 mt-4">
        <span class="text-[120px] text-red-400">
          <font-awesome-icon :icon="['fas', 'xmark']" />
        </span>
      </div>
    </div>

    <div class="mt-4">
      <p>備考欄</p>
      <p class="w-full p-3 bg-gray-200 text-sm min-h-[100px]">
        {{ customer.description }}
      </p>
    </div>

    <div class="mt-6 text-center">
      <router-link :to="{ name: 'edit-customer', params: { id: customer.id } }">
        <Btn text="編集" />
      </router-link>
    </div>

    <CustomInput model-value="" type="file" @change="previewImage" />

    <div class="max-h-[300px]">
      <img
        v-if="fileUrl"
        :src="fileUrl"
        alt="プレビュー画像"
        class="inline-block max-h-[300px]"
      />
    </div>
    <Btn text="送信" @click="submit" />
  </div>
  <span
    v-if="showBackButton"
    class="fixed bottom-20 right-3 block bg-customGray rounded-3xl py-3 px-1 text-white shadow-md shadow-gray-500"
    @click="goBack"
    >←戻る</span
  >
</template>
