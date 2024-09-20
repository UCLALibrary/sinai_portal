<template>
  <div>
    <h3 @click="toggle">
      <button type="button" class="" :aria-controls="'accordion-card-' + id" aria-expanded="false">
        <span class="title">
          {{ title }}
        </span>
        <span class="icon">
          <XMarkIcon
            v-if="isOpen"
            aria-hidden="true"
          />
          <PlusIcon
            v-else
            aria-hidden="true"
          />
        </span>
      </button>
    </h3>

    <div :class="['py-4', { 'hidden': !isOpen }]" :id="'accordion-card-' + id">
      <slot name="content" />
    </div>
  </div>
</template>

<script setup>
  import { ref, watch, onMounted } from 'vue'
  import { XMarkIcon, PlusIcon } from '@heroicons/vue/20/solid'
  import { uuid } from 'vue-uuid'

  const props = defineProps({  
    title: { type: String, required: true },
    open: { type: Boolean, required: false, default: true }
  })

  const emit = defineEmits()

  const id = ref(uuid.v1())

  const isOpen = ref(props.open)

  watch(() => isOpen.value, (newValue, oldValue) => {
    isOpen.value = newValue
  })

  const toggle = () => {
    isOpen.value = !isOpen.value;
    emit('open-accordion-card', isOpen.value)
  }

  onMounted(() => {
    const mediaQuery = window.matchMedia('(max-width: 1024px)')
    if (mediaQuery.matches) {
      isOpen.value = false
    }
    if (mediaQuery.addEventListener) {
        mediaQuery.addEventListener('change', e => {
            if (e.matches) {
              isOpen.value = false
            }
        });
    } else if (mediaQuery.addListener) { // Fallback for older browsers like Safari on macOS Catalina
        mediaQuery.addListener(e => {
            if (e.matches) {
              isOpen.value = false
            }
        });
    }
  })
</script>

<style lang="postcss" scoped>

  h3 {
    @apply flow-root
  }
  
  h3 button {
    @apply w-full flex items-start justify-between text-left text-black gap-x-4
  }

  h3 button .title {
    @apply text-sm md:text-base tracking-wide
  }

  h3 button .icon {
    @apply text-sm md:text-base
  }

  h3 button .icon svg {
    @apply h-6 w-6
  }
</style>
