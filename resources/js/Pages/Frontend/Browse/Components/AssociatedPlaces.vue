<template>
  <div v-for="place in places" class="mb-8">
    <p>
      {{ place.event.label }}: {{ [place.as_written, place.pref_name, place.value].filter(Boolean).join(' | ') }}
    </p>
    <p v-if="place.note && place.note.length > 0" class="indent">
      {{ place.note.join(", ") }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    places: {type: Array, required: false, default: []},
  },
};
</script>