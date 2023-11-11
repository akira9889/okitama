<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const props = defineProps({
  item: Object,
})

const emit = defineEmits(['clickMenuItem'])

const store = useStore()

const item = computed(() => {
  let item = {
    ...props.item,
  }

  if (!item.iconColor) {
    item.iconColor = 'text-customBlue'
  }

  return item
})

const isOpen = ref(false)
const router = useRouter()

const handleClick = () => {
  if (item.value.subItems) {
    isOpen.value = !isOpen.value
  } else if (item.value.type === 'logout') {
    store.dispatch('auth/logout')
  } else {
    goToRoute(item.value.route)
  }
}

const goToRoute = (route) => {
  if (route) {
    emit('clickMenuItem')
    router.push(route)
  }
}

const menuItemRef = ref(null)
const subItemListRef = ref(null)

watch(isOpen, () => {
  subItemListRef.value.classList.toggle('active')
  if (subItemListRef.value.classList.contains('active')) {
    subItemListRef.value.style.height = subItemListRef.value.scrollHeight + 'px'
    menuItemRef.value.style.marginBottom =
      subItemListRef.value.scrollHeight + 'px'
  } else {
    subItemListRef.value.style.height = '0'
    menuItemRef.value.style.marginBottom = '0'
  }
})
</script>

<template>
  <div
    ref="menuItemRef"
    class="sub-items flex justify-between items-center px-2 py-6 transition-colors hover:bg-black/20 border-b w-full relative cursor-pointer"
    @click="handleClick"
  >
    <div>
      {{ item.title }}
    </div>
    <template v-if="item.iconType === 'font-awesome'">
      <font-awesome-icon :icon="item.icon" :class="item.iconColor" />
    </template>
    <template v-else-if="item.subItems">
      <div :class="[{ active: isOpen }, 'plus']" />
    </template>
    <ul
      ref="subItemListRef"
      class="absolute top-full left-0 w-full sub-item-list"
    >
      <li
        v-for="(subItem, index) in item.subItems"
        :key="index"
        class="flex items-center justify-between px-2 py-6 transition-colors hover:bg-black/30 border-b w-full"
        @click.stop="goToRoute(subItem.route)"
      >
        <div>{{ subItem.title }}</div>
        <font-awesome-icon :icon="subItem.icon" :class="item.iconColor" />
      </li>
    </ul>
  </div>
</template>

<style lang="scss" scoped>
.sub-items {
  transition: 0.3s;
}

.sub-item-list {
  height: 0;
  overflow: hidden;
  transition: 0.3s;
}

.plus {
  &::before,
  &::after {
    content: '';
    display: inline-block;
    position: absolute;
    width: 12px;
    height: 2px;
    background-color: $base-color;
    top: 50%;
    transform: translateY(-50%);
    right: 10px;
  }

  &::after {
    transform: translateY(-50%) rotate(90deg);
    transition: all 0.3s;
  }

  &.active {
    &::after {
      transform: translateY(-50%) rotate(0deg);
    }
  }
}
</style>
