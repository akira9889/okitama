<script setup>
import { computed } from 'vue'
const emit = defineEmits(['clickModal'])

const props = defineProps({
  type: {
    type: String,
    default: 'normal',
  },
})

const modalClasses = computed(() => {
  const cls = []

  if (props.type === 'normal') {
    cls.push(`z-10`)
  } else if (props.type === 'hideFooter') {
    cls.push(`z-30`)
  }

  return cls.join(' ')
})
</script>

<template>
  <div :class="[modalClasses, 'modal']" @click="emit('clickModal')">
    <div @click.stop>
      <slot />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.modal {
  position: fixed;
  inset: 0;
  background-color: rgba($color: #000, $alpha: 70%);
}
</style>
