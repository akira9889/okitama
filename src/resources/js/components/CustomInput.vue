<script setup>
import { ref, watchEffect, computed } from 'vue'

const props = defineProps({
  modelValue: [String, Number, File, Boolean],
  id: [String, Number],
  label: String,
  type: {
    type: String,
    default: 'text',
  },
  name: String,
  required: Boolean,
  prepend: {
    type: String,
    default: '',
  },
  append: {
    type: String,
    default: '',
  },
  min: {
    type: Number,
    default: 1,
  },
  checked: {
    type: Boolean,
    default: false,
  },
  selectOptions: [Array, Object],
})

const id = computed(() => {
  if (props.id) return props.id
  return `id-${Math.floor(1000000 + Math.random() * 1000000)}`
})

const inputValue = ref(props.modelValue)

watchEffect(() => {
  inputValue.value = props.modelValue
})

const inputClasses = computed(() => {
  const cls = [
    `block px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:rin-customBlue-500 focus:border-customBlue-500 focus:z-10 sm:text-sm w-full h-full`,
  ]

  if (props.append && !props.prepend) {
    cls.push(`rounded-l-md`)
  } else if (props.prepend && !props.append) {
    cls.push(`rounded-r-md`)
  } else if (!props.prepend && !props.append) {
    cls.push('rounded-md')
  }

  return cls.join(' ')
})

const emit = defineEmits(['update:modelValue', 'change'])

function onChange(event) {
  const selectedIndex = event.target.selectedIndex
  const selectedText = event.target.options[selectedIndex].text
  let selectedValue = event.target.value
  if (!selectedValue) {
    selectedValue = ''
  }
  emit('change', { value: selectedValue, text: selectedText })
}

function onChangeCheck(event) {
  const value = event.target.checked
  const key = event.target.id
  emit('change', { key, value })
  emit('update:modelValue', event.target.checked)
}
</script>

<template>
  <div>
    <span
      v-if="prepend"
      class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-200 text-gray-500 text-sm"
    >
      {{ prepend }}
    </span>
    <template v-if="type === 'textarea'">
      <textarea
        :id="id"
        :name="name"
        :required="required"
        :value="inputValue"
        :class="inputClasses"
        :placeholder="label"
        @input="emit('update:modelValue', $event.target.value)"
      />
    </template>
    <template v-else-if="type === 'file'">
      <label
        class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer"
      >
        <input
          :id="id"
          :type="type"
          :name="name"
          :required="required"
          :value="inputValue"
          class="hidden"
          :placeholder="label"
          @input="emit('change', $event.target.files[0])"
        />
        画像を選択
      </label>
    </template>
    <template v-else-if="type === 'select'">
      <select
        :id="id"
        :name="name"
        :required="required"
        :value="inputValue"
        :class="inputClasses"
        :placeholder="label"
        @change="onChange($event)"
      >
        <option v-if="label" value="">{{ label }}</option>
        <option
          v-for="option of selectOptions"
          :key="option.key"
          :value="option.key"
        >
          {{ option.text }}
        </option>
      </select>
    </template>
    <template v-else-if="type === 'checkbox'">
      <div class="flex items-center">
        <input
          :id="id"
          :type="type"
          :name="name"
          :required="required"
          :checked="checked"
          class="h-3 w-3 text-customBlue focus:ring-indigo-500 border-gray-300 rounded"
          @change="onChangeCheck"
        />
      </div>
    </template>
    <template v-else>
      <div :class="[type === 'number' ? 'w-1/2' : 'w-full', 'relative']">
        <input
          :id="id"
          :type="type"
          :name="name"
          :required="required"
          :value="inputValue"
          :class="inputClasses"
          :min="min"
          step="1"
          :placeholder="label"
          autocomplete
          @input="emit('update:modelValue', $event.target.value)"
        />
      </div>
    </template>
    <span
      v-if="append"
      class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-200 text-gray-500 text-sm"
    >
      {{ append }}
    </span>
  </div>
</template>
