<script setup>
import { apiClient } from '@/services/API.js'
import Modal from '@/components/Modal.vue'
import Btn from '@/components/Btn.vue'
import Spinner from '@/components/Spinner.vue'
import { ref, watch, computed, onUnmounted } from 'vue'
import { useStore } from 'vuex'

const emit = defineEmits(['update:modelValue', 'resetFile'])

const props = defineProps({
  customerId: Number,
  modelValue: File,
})

const store = useStore()

const image = computed(() => props.modelValue)
const fileUrl = ref('')
const loading = ref(false)

watch(image, (newImage) => {
  if (newImage) {
    previewImage(newImage)
  }
})

onUnmounted(() => {
  emit('update:modelValue', null)
})

function previewImage(image) {
  if (image) {
    fileUrl.value = URL.createObjectURL(image)
  }
}

function clickModal() {
  if (loading.value) {
    return
  }
  fileUrl.value = ''
  emit('update:modelValue', null)
  emit('resetFile')
}

async function submit() {
  loading.value = true
  try {
    const formData = new FormData()
    if (image.value instanceof File) {
      formData.append('customer_id', props.customerId)
      formData.append('file', image.value)
    }
    await apiClient.post('/dropoff-history', formData)
    store.dispatch('toast/showToast', {
      message: '置き配写真を履歴に追加しました',
      delay: 5000,
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: '追加に失敗しました',
      type: 'error',
    })
  } finally {
    loading.value = false
    clickModal()
  }
}
</script>

<template>
  <Modal v-if="image" @click="clickModal">
    <div
      v-if="!loading"
      class="bg-white w-[70%] max-w-xl fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30 p-2 text-center"
    >
      <div>
        <p>履歴登録</p>
        <div class="p-2">
          <div class="border p-2">
            <img
              v-if="fileUrl"
              :src="fileUrl"
              alt="プレビュー画像"
              class="inline-block max-h-[400px] w-auto"
            />
          </div>
        </div>
        <Btn bg-color="primary" text="置き配履歴に登録" @click="submit" />
      </div>
    </div>
    <Spinner
      v-else
      class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
    />
  </Modal>
</template>
