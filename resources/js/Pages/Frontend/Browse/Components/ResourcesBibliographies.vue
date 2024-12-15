<template>
  <div v-if="bibliographies && bibliographies.length > 0">
    <div v-if="displayLabel" class="item-container">
      <span class="item-label">Bibliography</span>
      <div class="item-value">
        <template v-for="bibliography in bibliographies">
          <p>
            <a v-if="bibliography.url" :href="bibliography.url" target="_blank">
              {{ bibliography.formatted_citation }}
            </a>
            <span v-else>
              {{ bibliography.formatted_citation }}
            </span>
            <span v-if="bibliography.range">. {{ bibliography.range }}</span>
          </p>
        </template>
      </div>
    </div>

    <template v-else>
      <template v-for="bibliography in bibliographies">
        <p>
          <a v-if="bibliography.url" :href="bibliography.url" target="_blank">
            {{ bibliography.formatted_citation }}
          </a>
          <span v-else>
            {{ bibliography.formatted_citation }}
          </span>
          <span v-if="bibliography.range">. {{ bibliography.range }}</span>
        </p>
      </template>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    bibliographies: { type: Array, required: true },
    displayLabel: { type: Boolean, required: false, default: true },
  },
};
</script>
