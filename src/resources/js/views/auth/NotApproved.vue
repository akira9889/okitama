<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const store = useStore()
const router = useRouter()

const isApproved = computed(() => store.getters['auth/isApproved'])

onMounted(() => {
  if (isApproved.value) {
    router.push({ name: 'search-customer' })
  }
})
</script>
<template>
  <div
    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap"
  >
    <p>管理者から承認されていません。</p>
    <p>承認後、画面を更新してください。</p>
  </div>
</template>
