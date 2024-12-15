<template>
  <div v-if="inks && inks.length > 0" class="item-container">
    <span class="item-label">Ink</span>
    <div class="item-value">
      <template v-for="ink in inks">
        <p>
          <span class="block">
            {{ ink.locus }}<template v-if="ink.color && ink.color.length > 0">: {{ ink.color.join(', ') }}</template>
          </span>
          <template v-if="ink.note && ink.note.length > 0">
            <span v-for="note in ink.note" class="block">
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
    inks: {type: Array, required: false, default: []},
  },
};
</script>