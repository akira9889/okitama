<script setup>
import TableHeaderCell from '@/components/Table/TableHeaderCell.vue'
import TableDetailCell from '@/components/Table/TableDetailCell.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref } from 'vue'

const dropoffHistories = ref([])

onMounted(async () => {
  getDropoffHistories()
})

async function getDropoffHistories() {
  const { data } = await apiClient.get('/dropoff-history')
  dropoffHistories.value = data
}
</script>

<template>
  <h1 class="text-xl text-center">置き配履歴</h1>

  <div class="mt-4">
    <table class="w-full">
      <thead>
        <tr>
          <TableHeaderCell> 氏名 </TableHeaderCell>
          <TableHeaderCell> 住所 </TableHeaderCell>
          <TableHeaderCell class="w-[100px]"> 詳細 </TableHeaderCell>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(history, id) in dropoffHistories"
          :key="history.id"
          :class="{ 'bg-gray-100': id % 2 !== 0 }"
        >
          <TableDetailCell>
            {{ history.lastName }} {{ history.firstName }}
          </TableDetailCell>
          <TableDetailCell>
            <div class="flex w-full">
              {{ history.address }}
            </div>
          </TableDetailCell>
          <TableDetailCell>
            {{ history.id }}
          </TableDetailCell>
        </tr>
      </tbody>
    </table>
  </div>
</template>
