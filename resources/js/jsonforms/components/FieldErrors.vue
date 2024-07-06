<template>
  <div v-if="errors" :class="styles">
    {{ errors }}
  </div>
</template>

<script setup>
  defineProps({
    errors: Array,
    styles: String,
  })
</script>
