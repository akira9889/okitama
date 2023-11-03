<script setup>
import TableHeaderCell from '@/components/Table/TableHeaderCell.vue'
import TableDetailCell from '@/components/Table/TableDetailCell.vue'
import CustomInput from '@/components/CustomInput.vue'
import { apiClient } from '@/services/API.js'
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const currentDate = new Date()
const currentYear = currentDate.getFullYear()
const currentMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0')

const dropoffHistories = ref([])

const router = useRouter()

const form = ref({
  month: `${currentYear}-${currentMonth}`,
})

const monthsOptions = computed(() => {
  const options = []
  for (let i = 0; i < 12; i++) {
    const date = new Date(currentYear, currentDate.getMonth() - i, 1)
    const year = date.getFullYear()
    const month = (date.getMonth() + 1).toString().padStart(2, '0')
    const key = `${year}-${month}`
    const text = `${year}年${month}月`
    options.unshift({ key, text })
  }
  return options
})

onMounted(async () => {
  getDropoffHistories()
})

async function getDropoffHistories() {
  const { data } = await apiClient.get('/dropoff-history', { params: form.value })
  dropoffHistories.value = data
}

function changeMonth({value}) {
  form.value.month = value
  getDropoffHistories()
}

function redirect(param) {
  router.push({ name: 'dropoff-history.show', params: { id: param } })
}
</script>

<template>
  <h1 class="text-xl text-center">置き配履歴</h1>

  <div class="mt-4">
    <div class="flex justify-end">
      <div class="w-[130px]">
        <p class="text-center">過去12ヶ月分</p>
        <CustomInput
          v-model="form.month"
          type="select"
          :select-options="monthsOptions"
          @change="changeMonth"
        />
      </div>
    </div>
    <table v-if="dropoffHistories.length" class="w-full mt-4">
      <thead>
        <tr>
          <TableHeaderCell> 氏名 </TableHeaderCell>
          <TableHeaderCell> 住所 </TableHeaderCell>
          <TableHeaderCell>日時</TableHeaderCell>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(history, id) in dropoffHistories"
          :key="history.id"
          :class="{ 'bg-gray-100': id % 2 !== 0 }"
          @click="redirect(history.id)"
        >
          <TableDetailCell class="text-customBlue underline">
            {{ history.last_name }} {{ history.first_name }}
          </TableDetailCell>
          <TableDetailCell>
            <div class="flex w-full">
              {{ history.address }}
            </div>
          </TableDetailCell>
          <TableDetailCell>
            {{ history.created_at }}
          </TableDetailCell>
        </tr>
      </tbody>
    </table>
    <p
      v-else
      class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
    >
      履歴登録はありません
    </p>
  </div>
</template>
