<script setup>
import AwaitingUserModal from './AwaitingUserModal.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, onUnmounted, ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const users = ref([])

const selectUser = ref({})
const modalOpened = ref(false)

onMounted(() => {
  getUser()
  store.dispatch('returnButton/showButton', true)
  store.dispatch('returnButton/setRoute', { route: 'users' })
})

onUnmounted(() => {
  store.dispatch('returnButton/showButton', false)
})

async function getUser() {
  const { data } = await apiClient.get('/awaiting-user')
  users.value = data
}

function clickUser(user) {
  selectUser.value = user
  modalOpened.value = true
}
</script>

<template>
  <div>
    <h1 class="text-xl text-center">承認待ちユーザー</h1>

    <Transition name="modal">
      <AwaitingUserModal
        v-if="modalOpened"
        v-model="modalOpened"
        :user="selectUser"
        @edited-user="getUser"
      />
    </Transition>

    <ul class="flex flex-wrap">
      <li
        v-for="user in users"
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
