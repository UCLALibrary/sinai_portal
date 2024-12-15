<template>
  <div v-for="name in names" class="mb-8">
    <p>
      <template v-if="name.role.label">{{ name.role.label }}: </template>{{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
    </p>
    <p v-if="name.note && name.note.length > 0" class="indent">
      {{ name.note.join(", ") }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    names: {type: Array, required: false, default: []},
  },
};
</script>