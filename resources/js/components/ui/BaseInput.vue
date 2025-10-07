<script setup>
const props = defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  placeholder: String,
  // 1. ADICIONE A PROPRIEDADE 'REQUIRED' AQUI
  required: {
    type: Boolean,
    default: false
  }
})
const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="space-y-1">
        <label v-if="label" :for="id" class="form-label">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      class="form-input"
      @input="emit('update:modelValue', $event.target.value)"
    />
  </div>
</template>