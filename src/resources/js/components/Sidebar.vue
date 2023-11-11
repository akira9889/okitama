<script setup>
import MenuItem from '@/components/MenuItem.vue'
import { computed } from 'vue'
import { useStore } from 'vuex'

const emit = defineEmits(['onClickMenuItem'])
const store = useStore()
const isAdmin = computed(() => store.getters['auth/isAdmin'])

const menuItems = computed(() => {
  let items = [
    {
      title: '顧客検索',
      icon: ['fas', 'chevron-right'],
      iconType: 'font-awesome',
      route: '/search-customer',
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
        {
          title: 'ユーザー',
          route: '/users',
          icon: ['fas', 'chevron-right'],
          admin: true,
        },
        {
          title: 'エリア選択',
          route: '/delivery-area',
          icon: ['fas', 'chevron-right'],
        },
        { title: 'エリア登録', route: '/area', icon: ['fas', 'chevron-right'] },
      ],
    },
    {
      title: 'ログアウト',
      icon: ['fas', 'right-from-bracket'],
      iconType: 'font-awesome',
      iconColor: 'text-black',
      type: 'logout',
    },
  ]

  // 管理者でない場合はadminプロパティがtrueのアイテム以外をフィルタリング
  items = items.filter((item) => !item.admin || isAdmin.value)
  items.forEach((item) => {
    if (item.subItems) {
      item.subItems = item.subItems.filter(
        (subItem) => !subItem.admin || isAdmin.value,
      )
    }
  })

  return items
})

function forwardMenuItemClick() {
  emit('onClickMenuItem')
}
</script>

<template>
  <nav
    class="w-[180px] transition-all bg-white fixed top-0 bottom-0 left-full z-20"
  >
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
