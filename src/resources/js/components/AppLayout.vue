<script setup>
import { onMounted } from 'vue';
import store from '@/store';
import Sidebar from '@/components/Sidebar.vue';

onMounted(() => {
  store.dispatch('auth/getCurrentUser')
})
</script>

<template>
  <div class="min-h-[100vh] bg-gray-200">
    <!-- Sidebar -->
    <Sidebar />
    <!-- Sidebar -->

    <!-- Content -->
    <main class="p-6">
      <router-view></router-view>
    </main>
    <!-- Content -->
  </div>
</template>

<style scoped>
</style>
