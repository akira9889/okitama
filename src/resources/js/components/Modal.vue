<script setup>
const emit = defineEmits(['clickModal'])
</script>

<template>
  <div class="modal" @click="emit('clickModal')">
    <div @click.stop>
      <slot />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.modal {
  position: fixed;
  inset: 0;
  background-color: rgba($color: #000, $alpha: 70%);
  z-index: 20;
}
</style>
