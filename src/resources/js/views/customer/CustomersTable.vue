<script setup>
import { scrollToTop } from '@/constants.js'
import TableHeaderCell from '@/components/Table/TableHeaderCell.vue'
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import TableDetailCell from '@/components/Table/TableDetailCell.vue'
import { useStore } from 'vuex'

defineExpose({ tableScrollToTop })

const props = defineProps({
  customers: Array,
})

const store = useStore()

const customers = computed(() => props.customers)

const tableRef = ref(null)

const showScrollBar = ref(false)

const scrollProgress = ref(0)
const clientHeight = ref(0)
const scrollHeight = ref(0)

const SCROLL_BAR_HEIGHT_RATIO = 610
const scrollBarHeight = computed(
  () => (clientHeight.value / scrollHeight.value) * SCROLL_BAR_HEIGHT_RATIO,
)

onMounted(() => {
  setScrollBar()
  window.addEventListener('resize', setScrollBar)
})

onUnmounted(() => {
  window.removeEventListener('resize', setScrollBar)
})

watch(customers, async () => {
  await nextTick()
  setScrollBar()
})

const selectCustomer = (customer) => {
  store.commit('searchCustomer/SET_CUSTOMER_DETAIL', customer)
  store.commit('searchCustomer/SET_SHOW_BACK_BUTTON', true)
}

function setScrollBar() {
  const {
    scrollHeight: currentScrollHeight,
    clientHeight: currentClientHeight,
  } = tableRef.value

  if (currentClientHeight < currentScrollHeight) {
    showScrollBar.value = true
  } else {
    showScrollBar.value = false
  }

  clientHeight.value = currentClientHeight
  scrollHeight.value = currentScrollHeight
}

const MAX_SCROLL_PROGRESS = 1
const handleScroll = (e) => {
  const { scrollTop } = e.target
  const maxScroll = scrollHeight.value - clientHeight.value
  scrollProgress.value = Math.min(MAX_SCROLL_PROGRESS, scrollTop / maxScroll)
}

function tableScrollToTop() {
  scrollToTop(tableRef.value)
}
</script>

<template>
  <div ref="tableRef" class="customers-table" @scroll="handleScroll">
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
          <TableDetailCell class="text-customBlue underline">
            {{ customer.last_name }} {{ customer.first_name }}
          </TableDetailCell>
          <TableDetailCell>
            <div class="flex w-full">
              <div>
                {{ customer.town_name + customer.address_number }}
              </div>

              <div v-show="customer.building_name" class="ml-2">
                {{ customer.building_name }}
              </div>

              <div v-show="customer.company" class="ml-2">
                {{ customer.company }}
              </div>

              <div v-show="customer.room_number" class="ml-2">
                {{ customer.room_number }}
              </div>
            </div>
          </TableDetailCell>
          <TableDetailCell
            :class="customer.dropoffs.length ? 'bg-customGreen' : 'bg-red-300'"
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
    <div
      v-if="showScrollBar"
      class="scrollbar-wrap"
      :style="{
        height: clientHeight + 'px',
      }"
    >
      <div
        class="scrollbar"
        :style="{
          marginTop: (clientHeight - scrollBarHeight) * scrollProgress + 'px',
          height: scrollBarHeight + 'px',
        }"
      />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.customers-table {
  width: 100%;
  white-space: nowrap;
  overflow: auto;
  height: $customer-table-height;
  position: relative;

  &::-webkit-scrollbar {
    display: none;
  }
}

thead th {
  position: sticky;
  top: 0;
  z-index: 1;
}

.scrollbar-wrap {
  width: 6px;
  position: fixed;
  top: 0;
  z-index: 2;
  right: 2px;
  background: #efefef;
  overflow: hidden;
}

.scrollbar {
  margin: 0 auto;
  width: 4px;
  background: #bebebe;
  border-radius: 8px;
}
</style>
