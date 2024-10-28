<template>
  <VFadeTransition>
    <VAlert
      v-if="alertData"
      v-model="showAlert"
      :type="alertData.type"
      dismissible
      closable>
      <div v-html="alertData.message"></div>
    </VAlert>
  </VFadeTransition>
</template>

<script setup>
  import { ref } from 'vue'
  import useEmitter from '@/composables/useEmitter'

  const emitter = useEmitter()

  const showAlert = ref(false)

  const alertData = ref(null)

  emitter.on('show-dismissable-alert', (payload) => {
    showAlert.value = true
    alertData.value = payload
    if (payload && payload.hasOwnProperty('timeout')) {
      setTimeout(() => {
        showAlert.value = false
        alertData.value = null
      }, payload.timeout)
    }
  })
</script>

<style lang="postcss" scoped>
  .v-alert {
    @apply fixed top-12 left-1/2 transform -translate-x-1/2 w-fit max-w-xl
  }
</style>