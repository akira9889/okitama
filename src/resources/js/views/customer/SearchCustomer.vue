<script setup>
import { apiClient } from '@/services/API.js'
import { scrollToTop } from '@/constants.js'
import { ref, reactive, onMounted, computed, onUnmounted, watch } from 'vue'
import Spinner from '@/components/Spinner.vue'
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

const deliveryAreas = ref([])

const loading = ref(false)

const isCustomerDetailEmpty = computed(
  () => Object.keys(customerDetail.value).length === 0,
)
const isCustomersListEmptyOrMultiple = computed(
  () => customers.value.length === 0 || customers.value.length > 1,
)
const shouldCenterCustomersWrap = computed(
  () => isCustomerDetailEmpty.value && isCustomersListEmptyOrMultiple.value,
)

const shouldShowTable = computed(
  () => isCustomerDetailEmpty.value && customers.value.length > 1,
)

watch(
  () => form.town_id,
  () => {
    if (form.searchType === 'address') {
      submit()
    }
  },
)

onMounted(async () => {
  await setSelectedTowns()
  form.town_id = deliveryAreas.value[0].options[0].key
})

onUnmounted(clearState)

function clearState() {
  store.commit('searchCustomer/SET_CUSTOMER_DETAIL', {})
  store.commit('searchCustomer/SET_PREV_FORM', {})
}

async function submit() {
  loading.value = true

  try {
    await store.dispatch('searchCustomer/getCustomers', form)

    scrollToTop()

    if (form.searchType === 'name') {
      nameInputRef.value.blurInput()
    } else {
      addressInputRef.value.blurInput()
    }
  } catch {
    store.dispatch('toast/showToast', {
      message: '検索に失敗しました',
      type: 'error',
    })
  } finally {
    loading.value = false
  }
}

async function setSelectedTowns() {
  const { data } = await apiClient.get('/grouped-selected-towns')
  const transformedData = data.map((city) => {
    return {
      label: city.cityName,
      options: city.towns.map((town) => {
        return {
          key: town.townId,
          text: town.townName,
        }
      }),
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

  <div class="relative" :class="{ customers: isCustomerDetailEmpty }">
    <div
      class="customers-wrap"
      :class="{
        'absolute top-1/2 -translate-y-1/2': shouldCenterCustomersWrap,
      }"
    >
      <Spinner v-if="loading" />

      <CustomerDetail
        v-else-if="!isCustomerDetailEmpty"
        :customer="customerDetail"
      />
      <CustomersTable v-else-if="shouldShowTable" :customers="customers" />
      <div v-else class="text-center">顧客が見つかりません</div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.customers {
  min-height: calc(100vh - $header-height - $footer-height);
  min-height: calc(100dvh - $header-height - $footer-height);
}

.customers-wrap {
  width: 100%;
}
</style>
