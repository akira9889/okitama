<script setup>
import Modal from '@/components/Modal.vue'
import CustomInput from '@/components/CustomInput.vue'
import Btn from '@/components/Btn.vue'
import { apiClient } from '@/services/API.js'
import { onMounted, ref } from 'vue'

const props = defineProps({
  user: Object,
})

const emit = defineEmits(['update:modelValue', 'editedUser'])

const form = ref({})

onMounted(() => {
  form.value.is_admin = props.user.is_admin ? true : false
})

async function updateUser() {
  await apiClient.put(`/user/${props.user.id}`, form.value)
  emit('update:modelValue', false)
  emit('editedUser')
}

async function deleteUser() {
  if (
    !confirm(
      `${props.user.last_name} ${props.user.first_name}を削除してもいいですか？`,
    )
  ) {
    return
  }
  await apiClient.delete(`/user/${props.user.id}`)
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
          <Btn text="更新" bg-color="primary" @click="updateUser" />
        </div>
      </div>
    </div>
  </Modal>
</template>
