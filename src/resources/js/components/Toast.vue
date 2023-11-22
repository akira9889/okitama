<script setup>
import { ref, toRefs, onMounted, computed } from 'vue'
import store from '@/store'

const props = defineProps({
  toast: Object,
})

const { toast } = toRefs(props)

const DEFAULT_DELAY = 5000

const percent = ref(0)

onMounted(() => {
  if (!toast.value.delay && type.value === 'info') {
    toast.value.delay = DEFAULT_DELAY
  }
  // タイマーと進捗バーの設定
  if (toast.value.delay) {
    const startDate = Date.now()
    const futureDate = Date.now() + toast.value.delay

    const interval = setInterval(() => {
      const date = Date.now()
      percent.value = ((date - startDate) * 100) / (futureDate - startDate)
      if (percent.value >= 100) {
        clearInterval(interval)
      }
    }, 30)

    setTimeout(() => {
      close(toast.value.id)
    }, toast.value.delay)
  }
})

function close(id) {
  store.dispatch('toast/hideToast', id)
}

const type = computed(() => {
  return toast.value.type ? toast.value.type : 'info'
})

const bgColor = computed(() => {
  if (type.value === 'error') {
    return 'bg-red-500'
  } else {
    return 'bg-emerald-500'
  }
})
</script>

<template>
  <li
    v-if="toast"
    class="mt-2 py-3 pl-3 pr-10 text-white items-center relative inline-block"
    :class="bgColor"
  >
    <p class="font-semibold text-sm">
      {{ toast.message }}
    </p>
    <button
      class="w-[30px] h-[30px] rounded-full hover:bg-black/10 transition-colors absolute right-3 top-1/2 -translate-y-1/2"
      @click="close(toast.id)"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="inline-block h-6 w-6 absolute top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <div
      class="absolute left-0 bottom-0 right-0 h-[6px] bg-black/10"
      :style="{ width: `${percent}%` }"
    />
  </li>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
}
</style>
