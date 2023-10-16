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
  (newSearch) => {
    if (timer) {
      clearTimeout(timer)
    }
    if (!newSearch) {
      store.commit('searchCustomer/SET_CUSTOMERS', [])
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

const isActive = ref(false)

  const handleFocus = () => {
    isActive.value = true
  }

  const handleBlur = () => {
    isActive.value = false
  }
</script>

<template>
  <CustomInput
      v-model="form.search"
      type="search"
      label="検索"
      class="fixed z-30 left-1/2 bottom-[12px] -translate-x-1/2 w-2/3"
      @focus="handleFocus"
      @blur="handleBlur"
    />
  <div :class="['customers-wrap', { 'active': isActive }]">
    <CustomerDetail v-if="customers.length === 1" :customer="customers[0]" />
    <CustomersTable
      v-else-if="customers.length > 1"
      :customers="customers"
      @select-customer="handleSelectCustomer"
    />
    <div v-else class="text-center">顧客が見つかりません</div>
  </div>
</template>

<style lang="scss" scoped>
.customers-wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  overflow-y: scroll;
  transition: all .3s;
  bottom: 100%;
  
  &.active {
    height: 50dvh;
    position: absolute;
    bottom: 60px;
    left: 0;
    padding: 24px 8px;
  }
}
</style>
