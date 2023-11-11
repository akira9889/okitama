<script setup>
import Modal from '@/components/Modal.vue'
import CustomInput from '@/components/CustomInput.vue'
import Btn from '@/components/Btn.vue'
import { apiClient } from '@/services/API.js'
import { ref } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({
  user: Object,
})

const emit = defineEmits(['update:modelValue', 'editedUser'])

const store = useStore()

const form = ref({
  is_admin: false,
})

async function approvedUser() {
  try {
    await apiClient.put(`/awaiting-user/${props.user.id}`, form.value)
    emit('editedUser')

    store.dispatch('toast/showToast', {
      message: 'ユーザーを承認しました',
      delay: 5000,
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: 'ユーザーの承認に失敗しました',
      type: 'error',
    })
  } finally {
    emit('update:modelValue', false)
  }
}

async function deleteUser() {
  if (
    !confirm(
      `${props.user.last_name} ${props.user.first_name}を削除してもいいですか？`,
    )
  ) {
    return
  }

  await apiClient.delete(`/awaiting-user/${props.user.id}`)
  emit('update:modelValue', false)
  emit('editedUser')
}
</script>

<template>
  <Modal @click-modal="emit('update:modelValue', false)">
    <div
      class="bg-white w-[90%] max-w-xl fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30"
    >
      <div class="px-4 pt-5 pb-4">
        <div class="text-center">
          <font-awesome-icon :icon="['fas', 'user']" class="text-6xl" />
          <p>{{ user.last_name }} {{ user.first_name }}</p>
        </div>

        <div class="flex items-center justify-center mt-4">
          <label :for="user.id" class="inline-block mr-3">管理者機能</label>
          <CustomInput
            :id="user.id"
            v-model="form.is_admin"
            type="checkbox"
            class="w-5 h-5"
            :checked="form.is_admin"
          />
        </div>
        <p class="text-xs text-center mt-2">
          （ドライバーの編集や削除をすることができます。）
        </p>

        <div class="flex justify-center mt-4">
          <Btn text="削除" bg-color="danger" class="mr-6" @click="deleteUser" />
          <Btn text="承認" bg-color="primary" @click="approvedUser" />
        </div>
      </div>
    </div>
  </Modal>
</template>
