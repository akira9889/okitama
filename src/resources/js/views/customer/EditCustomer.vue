<script setup>
import { apiClient } from '@/services/API.js'
import { useRoute, useRouter } from 'vue-router'
import { ref, onMounted, watch } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import Btn from '@/components/Btn.vue'
import { useStore } from 'vuex'

const store = useStore()

const form = ref({
  dropoff_ids: [],
  town_id: '',
})

const errorMsg = ref({})

const deliveryAreas = ref([])
const dropoffs = ref([])
const is_dropoff_possible = ref(false)

onMounted(async () => {
  setSelectedTowns(), getDropoffPlace(), getCustomer()
})

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

const route = useRoute()
const router = useRouter()

watch(is_dropoff_possible, (isChecked) => {
  if (isChecked) {
    form.value.dropoff_ids = []
  }
})

async function getCustomer() {
  try {
    const { data } = await apiClient.get(`/customer/${route.params.id}`)
    form.value.last_name = data.last_name
    form.value.first_name = data.first_name
    form.value.last_kana = data.last_kana
    form.value.first_kana = data.first_kana
    form.value.company = data.company
    form.value.address_number = data.address_number
    form.value.building_name = data.building_name
    form.value.room_number = data.room_number
    form.value.dropoff_ids = data.dropoffs.map((dropoff) => dropoff.id)
    form.value.town_id = data.town_id
    form.value.description = data.description
    form.value.absence = !!data.absence
    form.value.only_amazon = !!data.only_amazon
  } catch {
    router.push({ name: 'notfound' })
  }
}

function changeTown({ value }) {
  form.value.town_id = value
}

function checkDropoffImpossible({ value }) {
  is_dropoff_possible.value = value
  if (value) {
    form.value.dropoff_ids = []
  }
}

function checkDropoffPlace({ key, value }) {
  is_dropoff_possible.value = false

  const dropoffId = Number(key)
  if (value) {
    form.value.dropoff_ids.push(dropoffId)
  } else {
    form.value.dropoff_ids = form.value.dropoff_ids.filter(
      (id) => id !== dropoffId,
    )
  }
}

async function submit() {
  try {
    await apiClient.put(`/customer/${route.params.id}`, form.value)
    errorMsg.value = {}
    store.dispatch('toast/showToast', {
      message: '顧客を更新しました',
    })
  } catch ({ response }) {
    errorMsg.value = response.data.errors
  }
}

async function getDropoffPlace() {
  const { data } = await apiClient.get('/dropoff')
  dropoffs.value = data
  if (!data.length) {
    is_dropoff_possible.value = true
  }
}

async function deleteCustomer() {
  try {
    if (!confirm('この顧客を削除してもよろしいですか？')) {
      return
    }

    await apiClient.delete(`/customer/${route.params.id}`)

    router.push({ name: 'search-customer' })

    store.dispatch('toast/showToast', {
      message: '顧客を削除しました',
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: '顧客の削除に失敗しました',
      type: 'error',
    })
  }
}
</script>

<template>
  <div class="relative">
    <h1 class="text-xl text-center">顧客編集</h1>
    <div
      class="w-12 h-12 rounded-full bg-customRed text-center leading-[48px] absolute right-0 top-0"
      @click="deleteCustomer"
    >
      <font-awesome-icon :icon="['fas', 'trash']" class="text-2xl text-white" />
    </div>
  </div>

  <form class="mt-6" @submit.prevent="submit">
    <div>
      <InputError :error-msg="errorMsg?.last_name" class="mb-2" />
      <InputError :error-msg="errorMsg?.first_name" class="mb-2" />
      <label for="last_name">ふりがな</label>
      <div class="flex">
        <CustomInput
          id="last_name"
          v-model="form.last_name"
          label="せい"
          class="name"
        />
        <CustomInput v-model="form.first_name" label="めい" class="name" />
      </div>
    </div>

    <div class="mt-6">
      <InputError :error-msg="errorMsg?.last_kana" class="mb-2" />
      <InputError :error-msg="errorMsg?.first_kana" class="mb-2" />
      <label for="last_kana">ふりがな</label>
      <div class="flex">
        <CustomInput
          id="last_kana"
          v-model="form.last_kana"
          label="せい"
          autocomplete="off"
          class="name"
        />
        <CustomInput
          v-model="form.first_kana"
          label="めい"
          autocomplete="off"
          class="name"
        />
      </div>
    </div>

    <div class="mt-6">
      <InputError :error-msg="errorMsg?.company" class="mb-2" />
      <label for="company">会社名</label>
      <CustomInput
        id="company"
        v-model="form.company"
        label="会社名"
        class="mt-1"
        autocomplete="off"
      />
    </div>

    <div class="mt-6">
      <label class="text-lg">住所</label>
      <div class="mt-2">
        <div class="flex items-center">
          <label for="town">エリア</label>
          <div
            class="ml-2 p-1 text-xs inline-block rounded-md bg-customBlue text-white"
          >
            必須
          </div>
        </div>
        <div class="flex items-baseline">
          <CustomInput
            id="town"
            v-model="form.town_id"
            class="mt-1 w-1/2"
            type="select"
            :select-options="deliveryAreas"
            @change="changeTown"
          />
        </div>
      </div>
    </div>

    <div class="mt-6">
      <InputError :error-msg="errorMsg?.address_number" class="mb-2" />
      <div class="flex items-center">
        <label for="address_number">番地（ハイフンあり）</label>
        <div
          class="ml-2 p-1 text-xs inline-block rounded-md bg-customBlue text-white"
        >
          必須
        </div>
      </div>
      <CustomInput
        id="address_number"
        v-model="form.address_number"
        label="1-2-3"
        class="mt-1"
      />
    </div>

    <div class="mt-6">
      <InputError :error-msg="errorMsg?.building_name" class="mb-2" />
      <label for="building_name">建物名</label>
      <CustomInput
        id="building_name"
        v-model="form.building_name"
        label="コーポ村山"
        autocomplete="off"
        class="mt-1"
      />
    </div>

    <div class="mt-6">
      <InputError :error-msg="errorMsg?.room_number" class="mb-2" />
      <label for="room_number">部屋番号</label>
      <CustomInput
        id="room_number"
        v-model="form.room_number"
        label="101"
        class="mt-1"
      />
    </div>
    <hr class="mt-6" />

    <label class="block mt-4">置き配</label>
    <div class="mt-1 flex flex-wrap">
      <div
        v-for="(dropoff, key) in dropoffs"
        :key="key"
        class="flex items-center mr-4"
      >
        <CustomInput
          :id="dropoff.id"
          type="checkbox"
          :checked="form.dropoff_ids.includes(dropoff.id)"
          class="w-3 h-3"
          @change="checkDropoffPlace"
        />
        <label :for="dropoff.id" class="ml-1 text-sm">{{ dropoff.name }}</label>
      </div>
      <div class="flex items-center mr-4">
        <CustomInput
          id="dropoff_impossible"
          type="checkbox"
          :checked="is_dropoff_possible"
          class="w-3 h-3"
          @change="checkDropoffImpossible"
        />
        <label for="dropoff_impossible" class="ml-1 text-sm">不可</label>
      </div>
    </div>

    <div class="mt-4 flex items-center">
      <label for="absence" class="mr-2 whitespace-nowrap">不在時置き配</label>
      <CustomInput
        id="absence"
        v-model="form.absence"
        type="checkbox"
        :checked="form.absence"
        class="w-3 h-3"
      />
    </div>

    <div class="mt-4 flex items-center">
      <label for="only_amazon" class="mr-2 whitespace-nowrap"
        >Amazonの荷物のみ置き配可</label
      >
      <CustomInput
        id="only_amazon"
        v-model="form.only_amazon"
        type="checkbox"
        class="w-3 h-3"
        :checked="form.only_amazon"
      />
    </div>

    <div class="mt-6">
      <label for="description">備考欄</label>
      <CustomInput
        id="description"
        v-model="form.description"
        type="textarea"
        class="mt-1"
      />
    </div>

    <div class="mt-6 text-center pb-6">
      <Btn type="submit" text="更新" bg-color="primary" />
    </div>
  </form>
</template>

<style scoped lang="scss">
.name {
  width: calc(50% - 15px);

  & + & {
    margin-left: 30px;
  }
}
</style>
