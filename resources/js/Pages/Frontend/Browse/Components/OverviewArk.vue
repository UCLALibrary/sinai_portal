<template>
  <div v-if="ark && ark !== ''" class="item-container">
    <span class="item-label">Ark</span>
    <p class="item-value">
      {{ ark }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    ark: {type: Object, required: true},
  },
};
</script>