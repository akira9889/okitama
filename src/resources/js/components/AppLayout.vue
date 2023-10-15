<script setup>
import { onMounted, ref, computed } from 'vue'
import store from '@/store'
import Modal from '@/components/Modal.vue'
import Sidebar from '@/components/Sidebar.vue'
import Footer from '@/components/Footer.vue'
import Toast from '@/components/Toast.vue'

onMounted(() => {
  store.dispatch('auth/getCurrentUser')

  setFillHeight()

  // ウィンドウサイズが変更されたときに、再度高さを計算
  window.addEventListener('resize', setFillHeight)
})

const setFillHeight = () => {
  const vh = window.innerHeight * 0.01
  document.documentElement.style.setProperty('--vh', `${vh}px`)
}

const sidebarOpened = ref(false)

function toggleSidebar() {
  modalOpened.value = !modalOpened.value
  sidebarOpened.value = !sidebarOpened.value
}

const modalOpened = ref(false)

const toasts = computed(() => store.state.toast.toasts)
</script>

<template>
  <Transition name="modal">
    <Modal v-if="modalOpened" @click-modal="toggleSidebar" />
  </Transition>
  <!-- Sidebar -->
  <Sidebar
    :class="{ '-ml-[180px]': !sidebarOpened }"
    @on-click-menu-item="toggleSidebar"
  />
  <!-- Sidebar -->

  <div class="main-wrap bg-gray-200">
    <!-- Content -->
    <main class="px-2 py-4 pb-24 bg-white h-hull">
      <div class="max-w-4xl mx-auto">
        <router-view />
      </div>
    </main>
    <!-- Content -->
  </div>

  <!-- Footer -->
  <Footer :sidebar-opened="sidebarOpened" @toggle-sidebar="toggleSidebar" />
  <!-- Footer -->

  <div class="fixed right-[10px] bottom-20">
    <TransitionGroup name="toast" tag="ul">
      <Toast v-for="toast in toasts" :key="toast" :toast="toast" />
    </TransitionGroup>
  </div>
</template>

<style scoped>
.main-wrap {
  min-height: 100vh;
  min-height: calc(var(--vh, 1vh) * 100);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

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
