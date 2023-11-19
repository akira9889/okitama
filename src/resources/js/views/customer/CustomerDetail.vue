<script setup>
import CustomInput from '@/components/CustomInput.vue'
import CustomerDetailModal from './CustomerDetailModal.vue'
import Btn from '@/components/Btn.vue'
import { computed, onUnmounted, ref } from 'vue'
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

const dropoffImage = ref(null)
const reset = ref(false)

function changeImage(image) {
  reset.value = false
  dropoffImage.value = image
}

function resetFile() {
  dropoffImage.value = null
  reset.value = true
}
</script>

<template>
  <div class="w-full relative my-6">
    <div
      class="absolute w-12 h-12 bg-customGray rounded-full text-center leading-[48px] right-0"
    >
      <CustomInput
        type="file"
        class="absolute top-0 left-0 cursor-pointer w-full h-full"
        accept="image/*"
        :reset="reset"
        @change="changeImage"
      />
      <font-awesome-icon
        :icon="['fas', 'camera']"
        class="text-white text-2xl"
      />
      <span
        class="inline-block text-xs absolute top-full whitespace-nowrap left-1/2 -translate-x-1/2 translate-y-1"
        >置き配写真</span
      >
    </div>

    <CustomerDetailModal
      v-model="dropoffImage"
      :customer-id="customer.id"
      @reset-file="resetFile"
    />

    <div class="text-center">
      <h2 class="text-xl">顧客情報</h2>
      <div v-show="customer.company" class="mt-6">
        <p class="font-semibold">会社名</p>
        <p>{{ customer.company }}</p>
      </div>
      <div v-show="customer.last_name || customer.first_name" class="mt-6">
        <p class="font-semibold">氏名</p>
        <p class="text-xs">
          {{ customer.last_kana }} {{ customer.first_kana }}
        </p>
        <p>{{ customer.last_name }} {{ customer.first_name }}</p>
      </div>

      <div class="mt-4">
        <p class="font-semibold">住所</p>
        <p>
          {{ customer.town_name + customer.address_number }}
          <span v-show="customer.room_number"
            >&emsp;{{ customer.room_number }}</span
          >
        </p>
      </div>
    </div>

    <div v-if="customer.dropoffs.length" class="flex items-center mt-4">
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
  </div>

  <span
    v-if="showBackButton"
    class="fixed bottom-20 right-3 block bg-customGray rounded-3xl py-3 px-1 text-white shadow-md shadow-gray-500"
    @click="goBack"
    >←戻る</span
  >
</template>
