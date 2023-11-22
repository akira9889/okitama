<script setup>
import { apiClient } from '@/services/API.js'
import CustomInput from '@/components/CustomInput.vue'
import Btn from '@/components/Btn.vue'
import { ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const file = ref({})
const reset = ref(false)
const loading = ref(false)

function changeFile(csvFile) {
  reset.value = false
  file.value = csvFile
}

async function submit() {
  loading.value = true
  try {
    const formData = new FormData()
    if (file.value instanceof File) {
      formData.append('file', file.value)
    }

    await apiClient.post('/import-customer', formData)

    store.dispatch('toast/showToast', {
      message: 'インポートしました',
    })
  } catch {
    store.dispatch('toast/showToast', {
      message: 'インポートに失敗しました',
      type: 'error',
    })
  } finally {
    loading.value = false
  }
}
</script>
<template>
  <div>
    <h1 class="text-xl text-center">インポート</h1>

    <p class="mt-16">ファイルの形式はcsvファイルのみです。</p>

    <p>データの形式は以下の形式ファイルのみインポートが可能です。</p>

    <p>
      都道府県名、市区町村名、町域名、姓、名、住所のアドレス(ハイフン区切り)、部屋番号、置き配場所(、区切り),
      備考
    </p>

    <p>該当するデータがない場合は空欄にしてください。</p>

    <p>例）千葉県, 船橋市, 湊町, 2-10-25, ,山田, 太郎, 玄関、車庫,</p>

    <form enctype="multipart/form-data">
      <div
        class="mt-8 w-full h-[100px] relative border border-gray-700 flex items-center justify-center"
      >
        <CustomInput
          type="file"
          class="absolute top-0 left-0 w-full h-full"
          accept=".csv"
          :reset="reset"
          @change="changeFile"
        />
        <Btn text="ファイルを選択" bg-color="primary" />
        <p class="absolute bottom-2">{{ file.name }}</p>
      </div>
      <div class="mt-8 text-center">
        <Btn
          text="インポート"
          bg-color="primary"
          type="button"
          @click="submit"
        />
      </div>
    </form>
  </div>
</template>
