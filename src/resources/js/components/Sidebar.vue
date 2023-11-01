<script setup>
import MenuItem from '@/components/MenuItem.vue'
import { ref } from 'vue'

const emit = defineEmits(['onClickMenuItem'])

const menuItems = ref([
  {
    title: '顧客検索',
    icon: ['fas', 'chevron-right'],
    iconType: 'font-awesome',
    route: '/',
  },
  {
    title: '顧客追加',
    icon: ['fas', 'chevron-right'],
    iconType: 'font-awesome',
    route: '/register-customer',
  },
  {
    title: '置き配履歴',
    icon: ['fas', 'chevron-right'],
    iconType: 'font-awesome',
    route: '/dropoff-history',
  },
  {
    title: '設定',
    subItems: [
      { title: 'ユーザー', route: '/users', icon: ['fas', 'chevron-right'] },
      {
        title: 'エリア選択',
        route: '/delivery-area',
        icon: ['fas', 'chevron-right'],
      },
      { title: 'エリア登録', route: '/area', icon: ['fas', 'chevron-right'] },
    ],
    icon: '<div class="plus"></div>',
    iconType: 'custom-html',
    route: null,
  },
])

function forwardMenuItemClick() {
  emit('onClickMenuItem')
}
</script>

<template>
  <nav class="w-[180px] transition-all bg-white fixed top-0 bottom-0 z-20">
    <div class="sidebar-inner">
      <MenuItem
        v-for="(item, index) in menuItems"
        :key="index"
        :item="item"
        @click-menu-item="forwardMenuItemClick"
      />
    </div>
  </nav>
</template>

<style lang="scss" scoped>
.sidebar-inner {
  margin-top: $header-height;
}
</style>
