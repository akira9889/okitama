<script setup>
import Btn from '@/components/Btn.vue'
import CustomInput from '@/components/CustomInput.vue'
import InputError from '@/components/InputError.vue'
import AreaModal from '@/views/setting/user/Area/AreaModal.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref } from 'vue'


const areas = ref({})
const modalOpened = ref(false)

const form = ref({
  delete_towns: []
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
    const index = form.value.delete_towns.indexOf(key);
    form.value.delete_towns.splice(index, 1);
  }
}

async function deleteTowns() {
  if (!confirm(`本当に削除してもいいですか？`)) {
    return
  }
  errorMsg.value = {}
  try {
    await apiClient.delete('/area', {params: form.value})
    getRegisteredAreas()
    isEditingArea.value = false
    form.value.delete_towns = []
  } catch({ response }) {
    errorMsg.value = response.data.errors
  }

}

</script>

<template>
  <Transition name="modal">
    <AreaModal v-if="modalOpened" v-model="modalOpened" @on-submit="getRegisteredAreas" />
  </Transition>

  <h1 class="text-xl text-center">エリア一覧</h1>
  <div class="text-right">
    <Btn text="エリアを追加" type="primary" @click="modalOpened = true" class="ml-2" />
  </div>

  <InputError :errorMsg="errorMsg?.['database']" />

  <div v-for="(cities, prefecture) in areas" :key="prefecture" class="prefecture-item">
    <h2 class="text-md mt-4">{{ prefecture }}</h2>
    <div
      v-for="(towns, city) in cities"
      :key="city"
      class="ml-3 mt-4"
    >
      <h3 class="text-sm">{{ city }}</h3>
      <div class="town-container">
        <div v-for="(town, key) in towns"
          :key="key"
          class="flex mr-2 ml-3">
          <CustomInput v-show="isEditingArea" type="checkbox" :label="town" :id="key" @change="updateSelectedTowns" class="town-name text-xs"/>
          <p v-if="!isEditingArea" class="town-name text-xs">
            {{ town }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-center">
    <div v-if="Object.keys(areas).length">
      <Btn v-if="!isEditingArea" text="編集" @click="isEditingArea = true" />
      <div v-else>
        <Btn text="キャンセル" @click="isEditingArea = false"/>
        <Btn text="削除" type="danger" class="ml-2" @click="deleteTowns" :disabled="!form.delete_towns.length" />
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
