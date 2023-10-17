<script setup>
import { reactive, watch, onMounted, computed } from 'vue'
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

    //iosでテキストを確定しないでキーボードを閉じるとvalueが空になり顧客が一瞬からになるため。
    if (!newSearch && oldSearch) {
      return
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
</script>

<template>
  <CustomInput
      v-model="form.search"
      type="search"
      label="検索"
      :focus="true"
      class="fixed z-30 left-1/2 bottom-[12px] -translate-x-1/2 w-2/3"
      />
  <div class="customers-wrap">
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
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  padding: 24px 8px;
}
</style>
