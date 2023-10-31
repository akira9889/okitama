<script setup>
import { apiClient } from '@/services/API.js'
import { scrollToTop } from '@/constants.js'
import { ref, reactive, onMounted, computed, onUnmounted } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import CustomersTable from './CustomersTable.vue'
import CustomerDetail from './CustomerDetail.vue'
import { useStore } from 'vuex'
import { onBeforeRouteLeave } from 'vue-router'

onBeforeRouteLeave(clearState)

const store = useStore()
const form = reactive({
  searchQuery: '',
  searchType: 'name',
})

const customers = computed(() => store.state.searchCustomer.customers)
const customerDetail = computed(() => store.state.searchCustomer.customerDetail)

const nameInputRef = ref(null)
const addressInputRef = ref(null)
const customersTableRef = ref(null)

const deliveryAreas = ref([])

onMounted(async () => {
  await setSelectedTowns()
  form.town_id = deliveryAreas.value[0]?.key
})

onUnmounted(clearState)

function clearState() {
  store.commit('searchCustomer/SET_CUSTOMER_DETAIL', {})
  store.commit('searchCustomer/SET_PREV_FORM', {})
}

async function submit() {
  await store.dispatch('searchCustomer/getCustomers', form)
  scrollToTop()
  if (
    Object.keys(customerDetail.value).length === 0 &&
    customers.value.length > 1
  ) {
    customersTableRef.value.tableScrollToTop()
  }
  if (form.searchType === 'name') {
    nameInputRef.value.blurInput()
  } else {
    addressInputRef.value.blurInput()
  }
}

async function setSelectedTowns() {
  const { data } = await apiClient.get('/selected-towns')
  const transformedData = data.map((item) => {
    return {
      key: item.id,
      text: item.name,
    }
  })

  deliveryAreas.value = transformedData
}

function changeTown({ value }) {
  form.town_id = value
}
</script>

<template>
  <footer
    class="bg-black fixed bottom-0 left-0 w-[100vw] h-[60px] px-2 py-3 z-10"
  >
    <form class="w-full h-full flex items-center" @submit.prevent="submit">
      <div class="w-2/3 flex">
        <CustomInput
          v-if="form.searchType === 'name'"
          ref="nameInputRef"
          v-model="form.searchQuery"
          type="search"
          label="なまえ"
          :focus="true"
          class="w-full"
        />
        <div v-else class="flex w-full">
          <CustomInput
            id="town"
            v-model="form.town_id"
            class="w-1/2 h-full mr-1"
            type="select"
            :select-options="deliveryAreas"
            @change="changeTown"
          />
          <CustomInput
            ref="addressInputRef"
            v-model="form.searchAddress"
            type="search"
            label="番地"
            class="w-1/2"
          />
        </div>
      </div>
      <div class="text-white ml-2">
        <div class="flex items-center">
          <CustomInput
            id="by-name"
            v-model="form.searchType"
            type="radio"
            name="search-way"
            radio-value="name"
          />
          <label for="by-name" class="block text-sm ml-2">名前</label>
        </div>
        <div class="flex items-center">
          <CustomInput
            id="by-address"
            v-model="form.searchType"
            type="radio"
            name="search-way"
            radio-value="address"
          />
          <label for="by-address" class="block text-sm ml-2">住所</label>
        </div>
      </div>
    </form>
  </footer>

  <div class="customers">
    <div
      class="customers-wrap"
      :class="{
        'absolute top-1/2 -translate-y-1/2':
          (Object.keys(customerDetail).length === 0 && customers.length > 1) ||
          (Object.keys(customerDetail).length === 0 && customers.length === 0),
      }"
    >
      <CustomerDetail
        v-if="Object.keys(customerDetail).length > 0"
        :customer="customerDetail"
      />
      <CustomersTable
        v-else-if="
          Object.keys(customerDetail).length === 0 && customers.length > 1
        "
        ref="customersTableRef"
        :customers="customers"
      />
      <div v-else class="text-center">顧客が見つかりません</div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.customers {
  min-height: calc(100dvh - $header-height - $footer-height);
  position: relative;
}

.customers-wrap {
  width: 100%;
}
</style>
