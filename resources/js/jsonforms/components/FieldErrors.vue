<template>
  <div v-if="errors" :class="styles">
    {{ errors }}
  </div>
</template>

<script setup>
  defineProps({
    errors: String,
    styles: String,
  })
</script>
