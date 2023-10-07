<script setup>
import { onMounted, ref } from 'vue'
import store from '@/store'
import Modal from '@/components/Modal.vue'
import Sidebar from '@/components/Sidebar.vue'
import Footer from '@/components/Footer.vue'

onMounted(() => {
  store.dispatch('auth/getCurrentUser')
})

const sidebarOpened = ref(false)

function toggleSidebar() {
  modalOpened.value = !modalOpened.value
  sidebarOpened.value = !sidebarOpened.value
}

const modalOpened = ref(false)
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

  <div class="min-h-[100vh] bg-gray-200">
    <!-- Content -->
    <main class="px-2 py-4 pb-16 bg-white">
      <div class="max-w-4xl mx-auto">
        <router-view />
      </div>
    </main>
    <!-- Content -->
  </div>

  <!-- Footer -->
  <Footer :sidebar-opened="sidebarOpened" @toggle-sidebar="toggleSidebar" />
  <!-- Footer -->
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
