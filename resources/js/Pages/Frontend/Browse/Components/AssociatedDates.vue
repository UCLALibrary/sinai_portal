<template>
  <div v-for="date in dates" class="mb-8">
    <p>
      {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
    </p>
    <p v-if="date.note && date.note.length > 0" class="indent">
      {{ date.note.join(", ") }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    dates: {type: Array, required: false, default: []},
  },
};
</script>