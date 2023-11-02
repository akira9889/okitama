<script setup>
import TableHeaderCell from '@/components/Table/TableHeaderCell.vue'
import TableDetailCell from '@/components/Table/TableDetailCell.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const dropoffHistories = ref([])

const router = useRouter()

onMounted(async () => {
  getDropoffHistories()
})

async function getDropoffHistories() {
  const { data } = await apiClient.get('/dropoff-history')
  dropoffHistories.value = data
}

function redirect(param) {
  router.push({ name: 'dropoff-history.show', params: { id: param } })
}
</script>

<template>
  <h1 class="text-xl text-center">置き配履歴</h1>

  <div class="mt-4">
    <table v-if="dropoffHistories.length" class="w-full">
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
    <p v-else class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">履歴登録はありません</p>
  </div>
</template>
