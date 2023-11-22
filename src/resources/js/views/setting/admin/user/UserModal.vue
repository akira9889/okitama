<script setup>
import Modal from '@/components/Modal.vue'
import CustomInput from '@/components/CustomInput.vue'
import Btn from '@/components/Btn.vue'
import { apiClient } from '@/services/API.js'
import { computed, onMounted, ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

const props = defineProps({
  user: Object,
})

const emit = defineEmits(['update:modelValue', 'editedUser'])

const store = useStore()
const router = useRouter()

const form = ref({})
const authUser = computed(() => store.getters['auth/authUser'])

onMounted(() => {
  form.value.is_admin = props.user.is_admin ? true : false
})

async function updateUser() {
  try {
    //自分自身の管理者権限を剥奪する時
    if (authUser.value.id === props.user.id && !form.value.is_admin) {
      if (
        !confirm(
          '以後ユーザーに関する操作やアクセスができなくなりますが、よろしいですか？',
        )
      ) {
        return
      } else {
        await apiClient.put(`/user/${props.user.id}`, form.value)
        store.dispatch('auth/getAuthUser')
        router.push({ name: 'search-customer' })
      }
    } else {
      await apiClient.put(`/user/${props.user.id}`, form.value)
      emit('editedUser')
    }
    store.dispatch('toast/showToast', {
      message: 'ユーザー情報を更新しました',
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: 'ユーザー情報の更新失敗しました',
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

  await apiClient.delete(`/user/${props.user.id}`)

  if (authUser.value.id === props.user.id) {
    router.push({ name: 'login' })
  } else {
    emit('update:modelValue', false)
    emit('editedUser')
  }
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
