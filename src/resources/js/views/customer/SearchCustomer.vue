<script setup>
import { scrollToTop } from '@/constants.js'
import { ref, reactive, onMounted, computed } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import CustomersTable from './CustomersTable.vue'
import CustomerDetail from './CustomerDetail.vue'
import { useStore } from 'vuex'

const store = useStore()
const form = reactive({
  search: '',
})

const customers = computed(() => store.state.searchCustomer.customers)

const customInputRef = ref(null)
const customersTableRef = ref(null)

const onSelectCustomer = (customer) => {
  customers.value = [customer]
}

onMounted(() => {
  store.dispatch('searchCustomer/getCustomers', form)
})

async function submit() {
  await store.dispatch('searchCustomer/getCustomers', form)
  customInputRef.value.blurInput()
  scrollToTop()
  customersTableRef.value.tableScrollToTop()
}
</script>

<template>
  <form @submit.prevent="submit">
    <CustomInput
      ref="customInputRef"
      v-model="form.search"
      type="search"
      label="検索"
      :focus="true"
      class="fixed z-30 left-1/2 bottom-[12px] -translate-x-1/2 w-2/3"
    />
  </form>
  <div class="customers">
    <div
      class="customers-wrap"
      :class="{ 'absolute top-1/2 -translate-y-1/2': customers.length !== 1 }"
    >
      <CustomerDetail v-if="customers.length === 1" :customer="customers[0]" />
      <CustomersTable
        v-else-if="customers.length > 1"
        ref="customersTableRef"
        :customers="customers"
        @select-customer="onSelectCustomer"
      />
      <div v-else class="text-center">顧客が見つかりません</div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.customers {
  min-height: calc(100dvh - 76px);
  position: relative;
}

.customers-wrap {
  width: 100%;
  padding-bottom: 24px;
}
</style>
