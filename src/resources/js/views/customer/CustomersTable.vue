<script setup>
import TableHeaderCell from '@/components/Table/TableHeaderCell.vue'
import { computed } from 'vue'
import TableDetailCell from '../../components/Table/TableDetailCell.vue'
import { useStore } from 'vuex'
const props = defineProps({
  customers: Array,
})

const store = useStore()

const customers = computed(() => props.customers)

const selectCustomer = (customer) => {
  store.commit('searchCustomer/SET_CUSTOMERS', [customer])
}
</script>

<template>
  <div class="w-full h-full whitespace-nowrap overflow-auto">
    <table class="w-full">
      <thead>
        <tr>
          <TableHeaderCell> 氏名 </TableHeaderCell>
          <TableHeaderCell> 住所 </TableHeaderCell>
          <TableHeaderCell class="w-[100px]"> 置き配 </TableHeaderCell>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(customer, id) in customers"
          :key="customer.id"
          :class="{ 'bg-gray-100': id % 2 !== 0 }"
          @click="selectCustomer(customer)"
        >
          <TableDetailCell class="border-b p-2 text-customBlue underline">
            {{ customer.last_name }} {{ customer.first_name }}
          </TableDetailCell>
          <TableDetailCell class="border-b p-2">{{
            customer.address
          }}</TableDetailCell>
          <TableDetailCell
            :class="[
              customer.dropoffs.length ? 'bg-customGreen' : 'bg-red-300',
              'border-b p-2',
            ]"
          >
            <span v-for="(dropoff, index) in customer.dropoffs" :key="index">
              {{ dropoff.name
              }}<span v-if="index < customer.dropoffs.length - 1">, </span>
            </span>
            <span v-if="!customer.dropoffs.length"
              ><font-awesome-icon :icon="['fas', 'xmark']"
            /></span>
          </TableDetailCell>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
thead th {
  position: sticky;
  top: 0;
  z-index: 1;
}
</style>
