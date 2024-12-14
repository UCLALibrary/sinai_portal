<template>
  <p v-if="summary && summary !== ''" :class="['italic', marginBottom ? 'mb-8' : '']">
    {{ summary }}
  </p>
</template>

<script>
export default {
  props: {
    summary: { type: String, required: false, default: '' },
    marginBottom: { type: Boolean, default: true },
  },
};
</script>
