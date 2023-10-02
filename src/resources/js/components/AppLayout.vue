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
    <Modal v-if="modalOpened" @clickModal="toggleSidebar" />
  </Transition>
  <!-- Sidebar -->
  <Sidebar
    :class="{ '-ml-[180px]': !sidebarOpened }"
    @onClickMenuItem="toggleSidebar"
  />
  <!-- Sidebar -->

  <div class="min-h-[100vh] bg-gray-200">
    <!-- Content -->
    <main class="p-6">
      <router-view />
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
