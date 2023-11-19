<script setup>
import { apiClient } from '@/services/API.js'
import { useRoute } from 'vue-router'
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

watch(is_dropoff_possible, (isChecked) => {
  if (isChecked) {
    form.value.dropoff_ids = []
  }
})

async function getCustomer() {
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
      delay: 5000,
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
</script>

<template>
  <h1 class="text-xl text-center">顧客編集</h1>
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
        <label for="town">エリア</label>
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
      <label for="address_number">番地（ハイフンあり）</label>
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
          @change="checkDropoffPlace"
        />
        <label :for="dropoff.id" class="ml-1 text-sm">{{ dropoff.name }}</label>
      </div>
      <div class="flex items-center mr-4">
        <CustomInput
          id="dropoff_impossible"
          type="checkbox"
          :checked="is_dropoff_possible"
          @change="checkDropoffImpossible"
        />
        <label for="dropoff_impossible" class="ml-1 text-sm">不可</label>
      </div>
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
