<script setup>
import { apiClient } from '@/services/API.js'
import { DROPOFF_PLACE_ID } from '@/constants.js'
import { ref, onMounted } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import Btn from '@/components/Btn.vue'

const form = ref({
  dropoff_ids: [],
  is_checked_default: false,
  town_id: '',
})

const errorMsg = ref({})

const deliveryAreas = ref([])
const dropoffs = ref([])

onMounted(async () => {
  // setSelectedTownsとgetDropoffPlaceを並行で実行
  const townsAndDropoffsPromise = Promise.all([
    setSelectedTowns(),
    getDropoffPlace(),
  ])

  // getDefaultTownは別で実行
  const defaultTownId = await getDefaultTown()

  // 並行処理の結果を待つ
  await townsAndDropoffsPromise

  form.value.town_id = defaultTownId || deliveryAreas.value[0]?.key
  form.value.dropoff_ids.push(DROPOFF_PLACE_ID.ENTRANCE)
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

async function getDefaultTown() {
  const { data } = await apiClient.get('/default-town')
  return data.id
}

function changeTown({ value }) {
  form.value.town_id = value
}

function checkDropoffPlace({ key, value }) {
  if (value) {
    form.value.dropoff_ids.push(key)
  } else {
    const index = form.value.dropoff_ids.indexOf(key)
    form.value.dropoff_ids.splice(index, 1)
  }
}

async function submit() {
  try {
    await apiClient.post('/customer', form.value)
  } catch ({ response }) {
    errorMsg.value = response.data.errors
  }
}

async function getDropoffPlace() {
  const { data } = await apiClient.get('/dropoff')
  dropoffs.value = data
}
</script>

<template>
  <h1 class="text-xl text-center">顧客登録</h1>
  <form class="mt-6" @submit.prevent="submit">
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
          <div class="flex items-baseline ml-3">
            <CustomInput
              id="default_town"
              v-model="form.is_checked_default"
              type="checkbox"
            />
            <label for="default_town" class="ml-2 text-sm"
              >デフォルト（追加時にこの町域が最初に選択されます。）</label
            >
          </div>
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
    <div class="flex mt-4">
      <label class="mr-2">置き配場所</label>
      <div
        v-for="(dropoff, key) in dropoffs"
        :key="key"
        class="flex items-center mr-4"
      >
        <CustomInput
          :id="dropoff.id"
          type="checkbox"
          :checked="form.dropoff_ids.includes(Number(dropoff.id))"
          @change="checkDropoffPlace"
        />
        <label :for="dropoff.id" class="ml-1 text-sm">{{ dropoff.name }}</label>
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
      <Btn type="submit" text="追加" bg-color="primary" />
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
