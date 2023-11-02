<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
const emit = defineEmits(['toggle-sidebar'])

const props = defineProps({
  sidebarOpened: Boolean,
})

const openMenuBtn = ref(props.sidebarOpened)

const store = useStore()
const router = useRouter()

const showReturnButton = computed(() => store.state.returnButton.show)
const returnRoute = computed(() => store.state.returnButton.returnRoute)

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

function clickReturnBtn() {
  router.push({
    name: returnRoute.value.name,
    params: returnRoute.value.params,
  })
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
    <div v-if="showReturnButton" class="text-white" @click="clickReturnBtn">
      ←戻る
    </div>
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
  display: flex;
  flex-direction: row-reverse;
  justify-content: space-between;
  align-items: center;
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
