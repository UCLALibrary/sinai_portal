<template>
  <div v-if="references && references.length > 0" class="item-container">
    <span class="item-label">References</span>
    <div class="item-value">
      <template v-for="reference in references">
        <p>
          <span class="block">
            {{ reference.short_title }}, {{ reference.range }}<span v-if="reference.alt_shelf">. Reference mark: {{ reference.alt_shelf }}</span>
          </span>
          <span v-for="note in reference.note" class="block">
            {{ note }}
          </span>
        </p>
      </template>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    references: {type: Array, required: true},
  },
};
</script>