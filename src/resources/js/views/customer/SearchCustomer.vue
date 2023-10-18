<script setup>
import { ref, reactive, watch, onMounted, computed } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import CustomersTable from './CustomersTable.vue'
import CustomerDetail from './CustomerDetail.vue'
import { useStore } from 'vuex'

const store = useStore()
const form = reactive({
  search: '',
})

const customers = computed(() => store.state.searchCustomer.customers)

let timer = null

watch(
  () => form.search,
  (newSearch, oldSearch) => {
    if (timer) {
      clearTimeout(timer)
    }

    console.log('New search:', newSearch)
    console.log('Old search:', oldSearch)

    //iosでテキストを確定しないでキーボードを閉じるとvalueが空になり顧客が一瞬からになるため。
    if (!newSearch && oldSearch) {
      return
    }

    timer = setTimeout(() => {
      store.dispatch('searchCustomer/getCustomers', form)
    }, 1000)
  },
)

onMounted(() => {
  store.commit('searchCustomer/SET_CUSTOMERS', [])
})

const handleSelectCustomer = (customer) => {
  customers.value = [customer]
}

const isInputFocused = ref(false)

function activateInputFocus() {
  isInputFocused.value = true
}

const isActive = ref(false)

const activateCustomersWrap = () => {
  isActive.value = true
}

const deactivateCustomersWrap = () => {
  isActive.value = false
}
</script>

<template>
  <CustomInput
    v-model="form.search"
    type="search"
    label="検索"
    :focus="isInputFocused"
    class="fixed z-30 left-1/2 bottom-[12px] -translate-x-1/2 w-2/3"
    @clear-input="activateInputFocus"
    @focus="activateCustomersWrap"
    @blur="deactivateCustomersWrap"
  />
  <div class="customers">
    <div :class="['customers-wrap', { active: isActive }]">
      <CustomerDetail v-if="customers.length === 1" :customer="customers[0]" />
      <CustomersTable
        v-else-if="customers.length > 1"
        :customers="customers"
        @select-customer="handleSelectCustomer"
      />
      <div v-else class="text-center">顧客が見つかりません</div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.customers {
  height: calc(100vh - 76px);
  position: relative;
}

.customers-wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  overflow-y: scroll;
  padding-bottom: 8px;

  &.active {
    height: 50%;
    position: absolute;
    left: 0;
    top: 50%;
  }
}
</style>
