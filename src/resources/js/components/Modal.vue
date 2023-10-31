<script setup>
import { computed } from 'vue'

const emit = defineEmits(['clickModal'])
const props = defineProps({
  type: {
    type: String,
    default: 'normal',
  },
})

const modalClass = computed(() => {
  const cls = [`fixed inset-0 bg-black bg-opacity-70`]

  if (props.type === 'normal') {
    cls.push('z-40')
  } else if (props.type === 'uncoverHeader') {
    cls.push('z-20')
  }

  return cls.join(' ')
})
</script>

<template>
  <div :class="modalClass" @click="emit('clickModal')">
    <div @click.stop>
      <slot />
    </div>
  </div>
</template>
