<script setup>
import { apiClient } from '@/services/API.js'
import { scrollToTop } from '@/constants.js'
import { ref, reactive, onMounted, computed, onUnmounted } from 'vue'
import Spinner from '@/components/Spinner.vue'
import CustomInput from '@/components/CustomInput.vue'
import Btn from '@/components/Btn.vue'
import GuideBox from '@/components/GuideBox.vue'
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

const loading = computed(() => store.state.searchCustomer.loading)

const isCustomerDetailEmpty = computed(
  () => Object.keys(customerDetail.value).length === 0,
)
const isCustomersListEmptyOrMultiple = computed(
  () => customers.value.length === 0 || customers.value.length > 1,
)
const shouldSetPositionCustomersWrap = computed(
  () => isCustomerDetailEmpty.value && isCustomersListEmptyOrMultiple.value,
)

const shouldShowTable = computed(
  () => isCustomerDetailEmpty.value && customers.value.length > 1,
)

onMounted(async () => {
  await setSelectedTowns()
  form.town_id = deliveryAreas.value[0]?.options[0].key
})

onUnmounted(clearState)

function clearState() {
  store.commit('searchCustomer/SET_CUSTOMER_DETAIL', {})
  store.commit('searchCustomer/SET_PREV_FORM', {})
}

async function submit() {
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
  }
}

async function getSelectedTowns() {
  const { data } = await apiClient.get('/grouped-selected-towns')

  if (!data.length) {
    store.dispatch('toast/showToast', {
      message: '配達エリアが設定されていないです',
      type: 'error',
      route: { name: 'delivery-area' },
      linkText: 'エリア選択に進む',
    })
  }

  return data
}

async function setSelectedTowns() {
  const data = await getSelectedTowns()
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
  <div class="exclude-global-keydown">
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
        <div class="ml-auto md:ml-3">
          <Btn text="検索" @click="submit" />
        </div>
      </form>
    </footer>

    <div class="relative" :class="{ customers: isCustomerDetailEmpty }">
      <template v-if="shouldShowTable">
        <div class="absolute top-2 left-0 w-full">
          <div class="flex items-center text-xs justify-between w-full">
            <div>
              <span>玄</span>
              <span>→玄関</span>
            </div>
            <div>
              <span>ガ</span>
              <span>→ガスメータ</span>
            </div>
            <div>
              <span>車</span>
              <span>→車庫</span>
            </div>
            <div>
              <span>自</span>
              <span>→自転車</span>
            </div>
            <div>
              <span>他</span>
              <span>→その他</span>
            </div>
          </div>
        </div>

        <div class="absolute top-11 flex justify-around text-xs w-full">
          <div class="flex items-center">
            <div class="w-10 h-6 border border-black relative">
              <span class="inline-block absolute top-0 left-0 text-xs rounded-full bg-sky-300 w-4 h-4 text-center leading-4 text-white">不</span>
            </div>
            <span>•••</span>
            <p>不在時</p>
          </div>
          <div class="flex items-center">
            <div class="w-10 h-6 bg-customGreen" />
            <span>•••</span>
            <p>荷物全般</p>
          </div>
          <div class="flex items-center">
            <div class="w-10 h-6 bg-yellow-200" />
            <span>•••</span>
            <p>Amazonのみ</p>
          </div>
        </div>
      </template>

      <div
        class="customers-wrap"
        :class="{
          'customers-wrap-position': shouldSetPositionCustomersWrap,
        }"
      >
        <Spinner v-if="loading" />

        <CustomerDetail
          v-else-if="!isCustomerDetailEmpty"
          :customer="customerDetail"
        />
        <CustomersTable v-else-if="shouldShowTable" :customers="customers" />
        <div v-else>
          <GuideBox
            class="w-5/6 absolute left-1/2 -translate-x-1/2 -translate-y-[calc(100%+24px)]"
          >
            <p>名前または住所で検索してください。</p>
            <p>※ 会社は住所で検索してください。</p>
            <p>
              検索結果が１つしかない場合はテーブルは表示されず、そのまま詳細が表示されます。
            </p>
          </GuideBox>
          <p class="text-center">顧客が見つかりません</p>
        </div>
      </div>
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

.customers-wrap-position {
  position: absolute;
  top: 50%;
  transform: translateY(
    calc(
      -50% + ($customer-table-margin-top - $customer-table-margin-bottom) / 2
    )
  );
}
</style>
