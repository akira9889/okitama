<script setup>
import { onMounted, ref, computed } from 'vue'
import store from '@/store'
import Modal from '@/components/Modal.vue'
import Sidebar from '@/components/Sidebar.vue'
import Header from '@/components/Header.vue'
import Toast from '@/components/Toast.vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const wrapperClass = computed(() => {
  return route.path !== '/search-customer' ? 'my-6' : ''
})

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
    <Modal
      v-if="modalOpened"
      type="uncoverHeader"
      @click-modal="toggleSidebar"
    />
  </Transition>

  <!-- Header -->
  <Header :sidebar-opened="sidebarOpened" @toggle-sidebar="toggleSidebar" />
  <!-- Header -->

  <!-- Sidebar -->
  <Sidebar
    :class="{ '-translate-x-full': sidebarOpened }"
    @on-click-menu-item="toggleSidebar"
  />
  <!-- Sidebar -->

  <div class="main-wrap bg-white">
    <!-- Content -->
    <main class="main">
      <div class="max-w-4xl mx-auto w-full" :class="wrapperClass">
        <router-view />
      </div>
    </main>
    <!-- Content -->
  </div>

  <div class="fixed right-[10px] bottom-20">
    <TransitionGroup name="toast" tag="ul">
      <Toast v-for="toast in toasts" :key="toast" :toast="toast" />
    </TransitionGroup>
  </div>
</template>

<style lang="scss" scoped>
.main {
  padding: $header-height 0.5rem $footer-height;
  background-color: #fff;
}

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
