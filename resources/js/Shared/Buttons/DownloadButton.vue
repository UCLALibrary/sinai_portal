<template>
  <a :href="endpoint" class="button">
    &darr; {{ label }}
  </a>
</template>

<script setup>
  defineProps({
    endpoint: { type: String, required: true },
    label: { type: String, required: false, default: 'Download All Records' },
  })
</script>

<style lang="postcss" scoped>
  a.button {
    @apply inline-block px-2 py-1 mt-1 rounded-full bg-white shadow border-0 hover:bg-sinai-light-blue text-sm
  }
</style>
