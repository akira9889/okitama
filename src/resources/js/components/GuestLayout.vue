<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import Toast from '@/components/Toast.vue'

defineProps({
  title: String,
})

const store = useStore()
const toasts = computed(() => store.state.toast.toasts)
</script>

<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2
        class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
      >
        {{ title }}
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <slot />
    </div>

    <div class="fixed right-[10px] bottom-20">
      <TransitionGroup name="toast" tag="ul">
        <Toast v-for="toast in toasts" :key="toast" :toast="toast" />
      </TransitionGroup>
    </div>
  </div>
</template>
<style scoped>
.toast-move,
.toast-enter-active,
.toast-leave-active {
  transition: all 0.5s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
