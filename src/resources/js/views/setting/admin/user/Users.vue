<script setup>
import { apiClient } from '@/services/API.js'
import UserModal from './UserModal.vue'
import { onMounted, ref } from 'vue'

const adminUsers = ref([])
const commonUsers = ref([])

const modalOpened = ref(false)

async function getUser() {
  const { data } = await apiClient.get('/user')
  adminUsers.value = data.filter((user) => user.is_admin && user.is_approved)
  commonUsers.value = data.filter((user) => !user.is_admin && user.is_approved)
}

const selectUser = ref({})

onMounted(() => {
  getUser()
})

function clickUser(user) {
  selectUser.value = user
  modalOpened.value = true
}
</script>
<template>
  <div>
    <h1 class="text-xl text-center">ドライバー一覧</h1>
    <Transition name="modal">
      <UserModal
        v-if="modalOpened"
        v-model="modalOpened"
        :user="selectUser"
        @edited-user="getUser"
      />
    </Transition>

    <div class="mt-4 text-center">
      <div class="p-2 bg-yellow-200 rounded inline-block">
        <p>
          <span
            class="inline-block rounded-full bg-yellow-500 w-6 h-6 text-center leading-6"
            ><font-awesome-icon :icon="['fas', 'exclamation']"
          /></span>
          ユーザー承認リクエストがあります。
          <router-link
            :to="{ name: 'awaiting-users' }"
            class="text-sm text-blue-600"
            >進む→</router-link
          >
        </p>
      </div>
    </div>

    <div class="mt-4">
      <h2 class="font-semibold">管理者</h2>
      <ul class="flex flex-wrap">
        <li
          v-for="user in adminUsers"
          :key="user.id"
          class="mr-3 text-center"
          @click="clickUser(user)"
        >
          <div class="mt-4">
            <div class="text-5xl">
              <font-awesome-icon :icon="['fas', 'user']" />
            </div>
            <p class="text-sm">{{ user.last_name }} {{ user.first_name }}</p>
          </div>
        </li>
      </ul>
    </div>
    <div class="mt-4">
      <h2 class="font-semibold">ドライバー</h2>
      <ul class="flex flex-wrap mt-4">
        <li
          v-for="user in commonUsers"
          :key="user.id"
          class="mr-3 text-center"
          @click="clickUser(user)"
        >
          <div class="text-5xl">
            <font-awesome-icon :icon="['fas', 'user']" />
          </div>
          <p class="text-sm">{{ user.last_name }} {{ user.first_name }}</p>
        </li>
      </ul>
    </div>
  </div>
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
