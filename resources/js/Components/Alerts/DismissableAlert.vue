<template>
  <VFadeTransition>
    <VAlert
      v-if="alert !== null"
      :type="alert.type"
      dismissible
      closable>
      <div v-html="alert.message"></div>
    </VAlert>
  </VFadeTransition>
</template>

<script setup>
  import { ref } from 'vue'
  import useEmitter from '@/composables/useEmitter'

  const emitter = useEmitter()

  const alert = ref(null)

  emitter.on('show-dismissable-alert', (payload) => {
    alert.value = payload
    if (payload && payload.hasOwnProperty('timeout')) {
      setTimeout(() => {
        alert.value = null
      }, payload.timeout)
    }
  })
</script>

<style lang="postcss" scoped>
  .v-alert {
    @apply fixed top-12 left-1/2 transform -translate-x-1/2 w-fit max-w-xl
  }
</style>