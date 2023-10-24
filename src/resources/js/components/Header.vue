<script setup>
import { ref, watch } from 'vue'
const emit = defineEmits(['toggle-sidebar'])

const props = defineProps({
  sidebarOpened: Boolean,
})

const openMenuBtn = ref(props.sidebarOpened)

watch(
  () => props.sidebarOpened,
  (newVal) => {
    openMenuBtn.value = newVal
  },
)

function clickMenuBtn() {
  openMenuBtn.value = !openMenuBtn.value
  emit('toggle-sidebar')
}
</script>

<template>
  <div class="header">
    <button
      type="button"
      :class="[{ active: openMenuBtn }, 'menu-btn']"
      @click="clickMenuBtn"
    >
      <span />
      <span />
      <span />
    </button>
  </div>
</template>

<style lang="scss" scoped>
.header {
  background-color: #000;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: $header-height;
  padding: 0.75rem 0.5rem;
  z-index: 30;
}

.menu-btn {
  position: relative;
  width: 32px;
  height: 40px;

  &::after {
    content: 'menu';
    position: absolute;
    color: #fff;
    font-size: 10px;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
  }

  span {
    display: inline-block;
    height: 3px;
    background-color: #fff;
    width: 100%;
    position: absolute;
    transition: 0.3s;
    left: 0;

    &:first-child {
      top: 0;
    }

    &:nth-child(2) {
      top: 8px;
    }

    &:nth-child(3) {
      top: 16px;
    }
  }

  &.active {
    &::after {
      content: 'close';
    }

    span {
      display: inline-block;
      width: 32px;
      height: 3px;
      background-color: #fff;
      position: absolute;

      &:first-child {
        top: 8px;
        transform: rotate(45deg);
      }

      &:nth-child(2) {
        opacity: 0;
      }

      &:nth-child(3) {
        top: 8px;
        transform: rotate(-45deg);
      }
    }
  }
}
</style>
