<script setup>
import store from '@/store'
import Btn from '@/components/Btn.vue'
import CustomInput from '@/components/CustomInput.vue'
import GuideBox from '@/components/GuideBox.vue'
import AreaModal from '@/views/setting/user/Area/AreaModal.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref } from 'vue'

const areas = ref({})
const modalOpened = ref(false)

const form = ref({
  delete_towns: [],
})

const isEditingArea = ref(false)

const errorMsg = ref({})

async function getRegisteredAreas() {
  const { data } = await apiClient.get('/area')
  areas.value = data
}

onMounted(() => {
  getRegisteredAreas()
})

function updateSelectedTowns({ key, value }) {
  if (value) {
    form.value.delete_towns.push(key)
  } else {
    const index = form.value.delete_towns.indexOf(key)
    form.value.delete_towns.splice(index, 1)
  }
}

async function deleteTowns() {
  if (!confirm(`本当に削除してもいいですか？`)) {
    return
  }
  errorMsg.value = {}
  try {
    await apiClient.delete('/area', { params: form.value })
    getRegisteredAreas()
    isEditingArea.value = false
    form.value.delete_towns = []
    store.dispatch('toast/showToast', {
      message: 'エリアが削除されました。',
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: 'エリアの削除に失敗しました。',
      type: 'error',
    })
  }
}
</script>

<template>
  <Transition name="modal">
    <AreaModal
      v-if="modalOpened"
      v-model="modalOpened"
      @on-submit="getRegisteredAreas"
    />
  </Transition>

  <h1 class="text-xl text-center">エリア一覧</h1>

  <GuideBox class="mt-4">
    <p>こちらは全ドライバーが追加したエリアが表示されます。</p>
    <p>
      担当するエリアがない場合は右の「エリア追加」ボタンから追加してください。
    </p>
    <p>
      すでに追加されている場合は<router-link
        :to="{ name: 'delivery-area' }"
        class="font-semibold underline¥"
        >エリア選択ページ</router-link
      >でエリア選択してください。
    </p>
  </GuideBox>

  <div class="text-right mt-6">
    <Btn
      text="エリアを追加"
      bg-color="primary"
      class="ml-2"
      @click="modalOpened = true"
    />
  </div>

  <div
    v-for="(cities, prefecture) in areas"
    :key="prefecture"
    class="prefecture-item"
  >
    <h2 class="text-md mt-4">{{ prefecture }}</h2>
    <div v-for="(towns, city) in cities" :key="city" class="ml-3 mt-4">
      <h3 class="text-sm">{{ city }}</h3>
      <div class="town-container">
        <div v-for="(town, key) in towns" :key="key" class="flex">
          <div v-if="isEditingArea" class="flex items-center mr-3">
            <CustomInput
              :id="key"
              type="checkbox"
              class="w-3 h-3"
              @change="updateSelectedTowns"
            />
            <label :for="key" class="ml-1 text-sm">{{ town }}</label>
          </div>
          <p v-if="!isEditingArea" class="ml-6 text-sm">
            {{ town }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-center mt-6">
    <div v-if="Object.keys(areas).length">
      <Btn v-if="!isEditingArea" text="編集" @click="isEditingArea = true" />
      <div v-else>
        <Btn text="キャンセル" @click="isEditingArea = false" />
        <Btn
          text="削除"
          bg-color="danger"
          class="ml-2"
          :disabled="!form.delete_towns.length"
          @click="deleteTowns"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.town-container {
  display: flex;
  flex-wrap: wrap;
  margin-top: 4px;
}

.town-name {
  margin-bottom: 10px;
  margin-right: 10px;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
