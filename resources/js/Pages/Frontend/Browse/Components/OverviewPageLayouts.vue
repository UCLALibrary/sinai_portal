<template>
  <div v-if="layouts && layouts.length > 0" class="item-container">
    <span class="item-label">Page layout</span>
    <div class="item-value">
      <template v-for="layout in layouts">
        <p>
          <span class="block">
            {{ layout.locus }}: {{ layout.lines }} | {{ layout.columns }}
          </span>
          <span v-if="layout.dim">
            {{ layout.dim }}
          </span>
          <template v-if="layout.note && layout.note.length > 0">
            <span v-for="note in layout.note" class="block">
              {{ note }}
            </span>
          </template>
        </p>
      </template>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    layouts: {type: Array, required: false, default: []},
  },
};
</script>