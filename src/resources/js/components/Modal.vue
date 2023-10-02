<script setup>
const emit = defineEmits(['clickModal'])
</script>

<template>
  <div class="modal" @click="emit('clickModal')" />
</template>

<style lang="scss" scoped>
.modal {
  position: fixed;
  inset: 0;
  background-color: rgba($color: #000, $alpha: 70%);
}
</style>
