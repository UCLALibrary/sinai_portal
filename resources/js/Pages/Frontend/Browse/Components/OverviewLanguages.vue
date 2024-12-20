<template>
  <div v-if="languages && languages.length > 0" class="item-container">
    <span class="item-label">Primary language<template v-if="languages.length > 1">s</template></span>
    <p class="item-value">
      {{ languages.map(lang => lang.label).join(', ') }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    languages: {type: Array, required: true},
  },
};
</script>