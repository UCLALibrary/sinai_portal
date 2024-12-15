<template>
  <div v-if="notes && notes.length > 0" class="item-container">
    <span class="item-label">Foliation Note</span>
    <div class="item-value">
      <p v-for="note in notes">
        {{ note.value }}
      </p>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    notes: {type: Array, required: false, default: []},
  },
};
</script>