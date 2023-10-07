<script setup>
defineProps({
  errorMsg: Object,
})
</script>

<template>
<ul v-if="errorMsg" class="text-red-500 text-sm leading-4">
  <li v-for="(error, index) in errorMsg" :key="index"><p>{{ error }}</p></li>
    </ul>
</template>

<style scoped>

</style>
