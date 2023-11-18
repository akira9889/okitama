<script setup>
import { computed } from 'vue'
const props = defineProps({
  text: String,
  type: String,
  disabled: {
    type: Boolean,
    default: false,
  },
  bgColor: String,
})

const btnClasses = computed(() => {
  const cls = [`py-2 px-4 text-white rounded-3xl`]

  if (props.bgColor === 'primary') {
    cls.push(`bg-customBlue`)
    if (props.disabled) {
      cls.push('bg-sky-100')
    }
  } else if (props.bgColor === 'danger') {
    cls.push(`bg-customRed`)
    if (props.disabled) {
      cls.push('bg-red-100')
    }
  } else {
    cls.push(`bg-customGray`)
  }

  return cls.join(' ')
})
</script>

<template>
  <button :type="type" :class="btnClasses" :disabled="disabled">
    {{ text }}
  </button>
</template>
