<script setup>
import { apiClient } from '@/services/API.js'
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useStore } from 'vuex'

const route = useRoute()
const store = useStore()
const router = useRouter()

const DropoffHistory = ref({})

onMounted(async () => {
  await getDropoffHistory()
  store.dispatch('returnButton/showButton', true)
  store.dispatch('returnButton/setRoute', { route: 'dropoff-history' })
})

onUnmounted(() => {
  store.dispatch('returnButton/showButton', false)
})

async function getDropoffHistory() {
  try {
    const { data } = await apiClient.get(`/dropoff-history/${route.params.id}`)

    DropoffHistory.value = data
  } catch {
    router.push({ name: 'notfound' })
  }
}
</script>

<template>
  <div class="w-full relative">
    <div class="text-center">
      <h2 class="text-xl">置き配履歴</h2>

      <p class="text-xl mt-6 inline-block">
        {{ DropoffHistory.last_name }} {{ DropoffHistory.first_name }}
      </p>

      <p class="mt-4">住所</p>
      <p>
        {{ DropoffHistory.address }}
      </p>

      <p class="mt-4">置き配日時</p>
      <p>{{ DropoffHistory.created_at }}</p>

      <p class="mt-4">写真</p>
      <div class="text-center mt-2">
        <img
          :src="DropoffHistory.image_url"
          alt="置き配写真"
          class="inline-block max-h-[400px]"
        />
      </div>
    </div>
  </div>
</template>
