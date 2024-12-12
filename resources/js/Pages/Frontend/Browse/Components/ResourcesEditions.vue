<template>
  <div v-if="editions && editions.length > 0" class="item-container">
    <span class="item-label">Editions</span>
    <div class="item-value">
      <template v-for="edition in editions">
        <p>
          <span class="block">
            <a v-if="edition.url" :href="edition.url" target="_blank">
              {{ edition.formatted_citation }}
            </a>
            <span v-else>
              {{ edition.formatted_citation }}
            </span>
            <span v-if="edition.range">. {{ edition.range }}</span>
          </span>
          <span v-for="note in edition.note" class="block">
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
    editions: {type: Array, required: true},
  },
};
</script>