<template>
  <!-- Overlay -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleOverlayClick"
      >
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

        <!-- Modal container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal content -->
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <div
              v-if="modelValue"
              :class="modalClasses"
              @click.stop
            >
              <!-- Header -->
              <div 
                v-if="$slots.header || title || showClose"
                class="flex items-center justify-between p-6 border-b border-gray-200"
              >
                <div class="flex items-center">
                  <!-- Icon -->
                  <div
                    v-if="icon"
                    :class="iconWrapperClasses"
                  >
                    <component 
                      :is="icon" 
                      :class="iconClasses"
                    />
                  </div>

                  <!-- Title -->
                  <div>
                    <h3 
                      v-if="title"
                      class="text-lg font-semibold text-gray-900"
                      :class="{ 'ml-3': icon }"
                    >
                      {{ title }}
                    </h3>
                    <p 
                      v-if="subtitle"
                      class="mt-1 text-sm text-gray-500"
                      :class="{ 'ml-3': icon }"
                    >
                      {{ subtitle }}
                    </p>
                  </div>

                  <!-- Custom header -->
                  <slot name="header" />
                </div>

                <!-- Close button -->
                <button
                  v-if="showClose"
                  type="button"
                  class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-uema-500"
                  @click="closeModal"
                >
                  <span class="sr-only">Fechar</span>
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Body -->
              <div :class="bodyClasses">
                <slot />
              </div>

              <!-- Footer -->
              <div 
                v-if="$slots.footer || showDefaultFooter"
                class="flex items-center justify-end px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-3"
              >
                <slot name="footer">
                  <BaseButton
                    v-if="showDefaultFooter"
                    variant="outline"
                    @click="closeModal"
                  >
                    {{ cancelText }}
                  </BaseButton>
                  <BaseButton
                    v-if="showDefaultFooter"
                    variant="primary"
                    :loading="loading"
                    @click="$emit('confirm')"
                  >
                    {{ confirmText }}
                  </BaseButton>
                </slot>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import BaseButton from './BaseButton.vue'

const props = defineProps({
  // v-model para controlar visibilidade
  modelValue: {
    type: Boolean,
    default: false
  },

  // Conteúdo do header
  title: String,
  subtitle: String,
  icon: [String, Object],

  // Configurações do modal
  size: {
    type: String,
    default: 'md',
    validator: value => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
  },

  // Comportamento
  persistent: {
    type: Boolean,
    default: false
  },

  showClose: {
    type: Boolean,
    default: true
  },

  closeOnEsc: {
    type: Boolean,
    default: true
  },

  // Footer padrão
  showDefaultFooter: {
    type: Boolean,
    default: false
  },

  cancelText: {
    type: String,
    default: 'Cancelar'
  },

  confirmText: {
    type: String,
    default: 'Confirmar'
  },

  loading: {
    type: Boolean,
    default: false
  },

  // Estilo
  variant: {
    type: String,
    default: 'default',
    validator: value => ['default', 'success', 'warning', 'danger'].includes(value)
  },

  // Layout
  scrollable: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'close', 'confirm'])

// Classes do modal
const modalClasses = computed(() => {
  const classes = [
    'relative',
    'bg-white',
    'rounded-lg',
    'shadow-modal',
    'w-full',
    'mx-auto',
    'overflow-hidden'
  ]

  // Tamanhos
  const sizeClasses = {
    xs: ['max-w-xs'],
    sm: ['max-w-sm'],
    md: ['max-w-md'],
    lg: ['max-w-lg'],
    xl: ['max-w-xl'],
    '2xl': ['max-w-2xl'],
    full: ['max-w-full', 'mx-4']
  }
  classes.push(...sizeClasses[props.size])

  return classes
})

// Classes do body
const bodyClasses = computed(() => {
  const classes = ['p-6']

  if (props.scrollable) {
    classes.push('max-h-96', 'overflow-y-auto')
  }

  return classes
})

// Classes do wrapper do ícone
const iconWrapperClasses = computed(() => {
  const baseClasses = [
    'flex',
    'h-12',
    'w-12',
    'items-center',
    'justify-center',
    'rounded-full',
    'flex-shrink-0'
  ]

  const variantClasses = {
    default: ['bg-uema-100'],
    success: ['bg-success-100'],
    warning: ['bg-warning-100'],
    danger: ['bg-danger-100']
  }

  return [...baseClasses, ...variantClasses[props.variant]]
})

// Classes do ícone
const iconClasses = computed(() => {
  const baseClasses = ['h-6', 'w-6']

  const variantClasses = {
    default: ['text-uema-600'],
    success: ['text-success-600'],
    warning: ['text-warning-600'],
    danger: ['text-danger-600']
  }

  return [...baseClasses, ...variantClasses[props.variant]]
})

// Fechar modal
const closeModal = () => {
  emit('update:modelValue', false)
  emit('close')
}

// Click no overlay
const handleOverlayClick = (event) => {
  if (!props.persistent && event.target === event.currentTarget) {
    closeModal()
  }
}

// Tecla ESC
const handleEscKey = (event) => {
  if (props.closeOnEsc && event.key === 'Escape' && props.modelValue) {
    closeModal()
  }
}

// Event listeners
onMounted(() => {
  document.addEventListener('keydown', handleEscKey)
  
  // Prevent body scroll when modal is open
  if (props.modelValue) {
    document.body.style.overflow = 'hidden'
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscKey)
  document.body.style.overflow = 'auto'
})

// Watch modal value to control body scroll
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = 'auto'
  }
})
</script>