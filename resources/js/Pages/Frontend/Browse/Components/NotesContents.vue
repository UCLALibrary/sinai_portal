<template>
  <template v-if="notes && notes.length > 0">
    <p>
      <strong>Content Notes</strong>
    </p>
    <ul class="bulleted">
      <li v-for="note in notes">
        {{ note.value }}
      </li>
    </ul>
  </template>
</template>
<script>
export default {
  props: {
    notes: {type: Array, required: true},
  },
};
</script>