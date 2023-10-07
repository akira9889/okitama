<script setup>
import MenuItem from '@/components/MenuItem.vue'
import { ref } from 'vue'

const emit = defineEmits(['onClickMenuItem'])

const menuItems = ref([
  {
    title: 'トップ',
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
    <MenuItem
      v-for="(item, index) in menuItems"
      :key="index"
      :item="item"
      @click-menu-item="forwardMenuItemClick"
    />
  </nav>
</template>
