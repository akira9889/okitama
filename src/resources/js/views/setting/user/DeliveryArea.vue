<script setup>
import store from '@/store'
import Btn from '@/components/Btn.vue'
import CustomInput from '@/components/CustomInput.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref, computed } from 'vue'

const areas = ref({})

const form = ref({
  selectedTowns: [],
})

let firstSelectedTowns = []

const canSubmitUpdate = computed(() => {
  return (
    JSON.stringify(firstSelectedTowns.slice().sort()) !==
    JSON.stringify(form.value.selectedTowns.slice().sort())
  )
})

async function getRegisteredAreas() {
  const { data } = await apiClient.get('/area')
  areas.value = data
}

onMounted(() => {
  getRegisteredAreas()
  getSelectedTown()
})

async function getSelectedTown() {
  const { data } = await apiClient.get('/selected-towns')
  form.value.selectedTowns = data.map((town) => town.id)
  firstSelectedTowns = [...data.map((town) => town.id)]
}

function updateSelectedTowns({ key, value }) {
  const town_id = Number(key)
  if (value) {
    form.value.selectedTowns.push(town_id)
  } else {
    const index = form.value.selectedTowns.indexOf(town_id)
    form.value.selectedTowns.splice(index, 1)
  }
}

async function submit() {
  try {
    if (!canSubmitUpdate.value) {
      return
    }
    await apiClient.put('/delivery-area', form.value)
    store.dispatch('toast/showToast', {
      message: '配達エリアを更新しました。',
    })
    getSelectedTown()
  } catch {
    store.dispatch('toast/showToast', {
      message: '配達エリアの更新に失敗しました。',
      type: 'error',
    })
  }
}
</script>

<template>
  <h1 class="text-xl text-center">配達エリア</h1>

  <form @submit.prevent="submit">
    <div
      v-for="(cities, prefecture) in areas"
      :key="prefecture"
      class="prefecture-item"
    >
      <h2 class="text-md mt-4">{{ prefecture }}</h2>
      <div v-for="(towns, city) in cities" :key="city" class="ml-3 mt-4">
        <h3 class="text-sm">{{ city }}</h3>
        <div class="flex flex-wrap">
          <div
            v-for="(town, key) in towns"
            :key="key"
            class="flex items-center mr-3"
          >
            <CustomInput
              :id="key"
              type="checkbox"
              class="text-xs w-3 h-3"
              :checked="form.selectedTowns.includes(Number(key))"
              @change="updateSelectedTowns"
            />
            <label :for="key" class="ml-1 text-sm">{{ town }}</label>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <Btn
        text="更新"
        type="submit"
        bg-color="primary"
        :disabled="!canSubmitUpdate"
      />
    </div>
  </form>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
