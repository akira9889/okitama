<script setup>
import { computed } from 'vue'
const props = defineProps({
  text: String,
  type: String,
  disabled: {
    type: Boolean,
    default: false,
  },
})

const btnClasses = computed(() => {
  const cls = [`py-2 px-4 text-white rounded-3xl`]

  if (props.disabled) {
    cls.push('bg-red-100')
  } else {
    if (props.type === 'primary') {
      cls.push(`bg-customBlue`)
    } else if (props.type === 'danger') {
      cls.push(`bg-customRed`)
    } else {
      cls.push(`bg-customGray`)
    }
  }

  return cls.join(' ')
})
</script>

<template>
  <button :class="btnClasses" :disabled="disabled">{{ text }}</button>
</template>
