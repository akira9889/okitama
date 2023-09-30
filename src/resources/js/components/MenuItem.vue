<script setup>
import { ref, defineProps, watch } from 'vue';
import { useRouter } from 'vue-router';

const { item } = defineProps({
  item: Object
});

const isOpen = ref(false);
const router = useRouter();

const handleClick = () => {
  if (item.subItems) {
    isOpen.value = !isOpen.value;
  } else {
    goToRoute(item.route);
  }
};

const goToRoute = (route) => {
  if (route) {
    router.push(route);
  }
};

const menuItemRef = ref(null);
const subItemListRef = ref(null);

watch(isOpen, () => {
  console.log('実行');
  subItemListRef.value.classList.toggle('active')
  if (subItemListRef.value.classList.contains("active")) {
    subItemListRef.value.style.height = subItemListRef.value.scrollHeight + 'px';
    menuItemRef.value.style.marginBottom = subItemListRef.value.scrollHeight + 'px';
  } else {
    subItemListRef.value.style.height = "0";
    menuItemRef.value.style.marginBottom = "0"
  }
});

</script>

<template>
  <div v-if="item" ref="menuItemRef" @click="handleClick"
    class="sub-items flex justify-between items-center px-2 py-6 transition-colors hover:bg-black/20 border-b w-full relative cursor-pointer">
    <div>
      {{ item.title }}
    </div>
    <template v-if="item.iconType === 'font-awesome'">
      <font-awesome-icon :icon="item.icon" class="sub-item-icon" />
    </template>
    <template v-else-if="item.iconType === 'custom-html'">
      <div :class="[{ active: isOpen }, 'plus']"></div>
    </template>
    <ul ref="subItemListRef" class="absolute top-full left-0 w-full sub-item-list">
      <li v-for="(subItem, index) in item.subItems" :key="index" @click.stop="goToRoute(subItem.route)"
        class="flex items-center justify-between px-2 py-6 transition-colors hover:bg-black/30 border-b w-full">
          <div>{{ subItem.title }}</div>
          <font-awesome-icon :icon="subItem.icon" class="sub-item-icon" />
      </li>
    </ul>
  </div>
</template>


<style lang="scss" scoped>
.sub-items {
  transition: .3s;
}

.sub-item-list {
  height: 0;
  overflow: hidden;
  transition: .3s;
}

.sub-item-icon {
  color: $base-color;
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
    transition: all .3s;
  }

  &.active {
    &::after {
      transform: translateY(-50%) rotate(0deg);
    }
  }
}
</style>
