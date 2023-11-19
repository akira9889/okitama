<script setup>
import { apiClient } from '@/services/API.js'
import { DROPOFF_PLACE_ID, scrollToTop } from '@/constants.js'
import { ref, onMounted } from 'vue'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import Btn from '@/components/Btn.vue'
import { useStore } from 'vuex'

const store = useStore()

const DEFAULT_FORM = {
  last_name: '',
  first_name: '',
  last_kana: '',
  first_kana: '',
  dropoff_ids: [],
  is_checked_default: false,
  town_id: '',
}

const form = ref({ ...DEFAULT_FORM })

const errorMsg = ref({})

const deliveryAreas = ref([])
const dropoffs = ref([])

onMounted(async () => {
  await initializeForm()
})

async function initializeForm() {
  form.value = { ...DEFAULT_FORM }

  const townsAndDropoffsPromise = Promise.all([
    setSelectedTowns(),
    getDropoffPlace(),
  ])

  const defaultTownId = await getDefaultTown()

  await townsAndDropoffsPromise
  form.value.town_id = defaultTownId || deliveryAreas.value[0].options[0].key
  form.value.dropoff_ids.push(DROPOFF_PLACE_ID.ENTRANCE)
  scrollToTop()
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

async function getDefaultTown() {
  const { data } = await apiClient.get('/default-town')
  return data.id
}

function changeTown({ value }) {
  form.value.town_id = value
}

function checkDropoffPlace({ key, value }) {
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
    await apiClient.post('/customer', form.value)
    store.dispatch('toast/showToast', {
      message: '顧客を追加しました',
      delay: 5000,
    })
    initializeForm()
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
    <div>
      <InputError :error-msg="errorMsg?.last_name" class="mb-2" />
      <InputError :error-msg="errorMsg?.first_name" class="mb-2" />
      <label for="last_name">氏名</label>
      <div class="flex">
        <CustomInput
          id="last_name"
          v-model="form.last_name"
          label="姓"
          autocomplete="off"
          class="name"
        />
        <CustomInput
          v-model="form.first_name"
          label="名"
          autocomplete="off"
          class="name"
        />
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
        <div class="flex items-baseline mt-1">
          <CustomInput
            id="town"
            v-model="form.town_id"
            class="w-1/3"
            type="select"
            :select-options="deliveryAreas"
            autocomplete="off"
            @change="changeTown"
          />
          <div class="flex items-baseline ml-2 w-2/3">
            <div>
              <CustomInput
                id="default_town"
                v-model="form.is_checked_default"
                type="checkbox"
                class="w-3 h-3"
              />
            </div>
            <label for="default_town" class="text-sm ml-2"
              >デフォルト（次回以降、この町域が最初の選択されているようになります。）</label
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
        autocomplete="off"
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
          :checked="form.dropoff_ids.includes(dropoff.id)"
          class="w-3 h-3"
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
