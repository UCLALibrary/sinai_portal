<template>
  <div
    v-if="control.required || control.errors"
    class="text-xs text-red-500 ml-2">
    {{ control.errors }}
  </div>
</template>

<script setup>
  defineProps({
    control: Object,
  })
</script>
