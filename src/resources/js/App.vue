<script setup>
import { ref, onMounted, nextTick } from 'vue'

const isComposing = ref(false)

onMounted(async () => {
  await nextTick()

  document.addEventListener('compositionstart', () => {
    isComposing.value = true
  })

  document.addEventListener('compositionend', () => {
    isComposing.value = false
  })

  document.addEventListener('keydown', (e) => {
    if (
      isComposing.value ||
      e.target.closest('.exclude-global-keydown') ||
      e.target.tagName === 'TEXTAREA'
    ) {
      return
    }

    if (e.key === 'Enter' && e.target.form && e.target.type !== 'submit') {
      e.preventDefault()
      // フォーカスを次の要素に移動させる
      const formElements = Array.from(e.target.form.elements)
      const currentIndex = formElements.indexOf(e.target)
      const nextElement = formElements[currentIndex + 1]
      if (nextElement) {
        if (nextElement.type === 'checkbox') {
          return
        }
        nextElement.focus()
      }
    }
  })
})
</script>

<template>
  <router-view />
</template>
