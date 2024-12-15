<template>
  <div v-if="translations && translations.length > 0">
    <div v-if="displayLabel" class="item-container">
      <span class="item-label">Translations</span>
      <div class="item-value">
        <template v-for="translation in translations">
          <p>
            <span class="block">
              <a v-if="translation.url" :href="translation.url" target="_blank">
                {{ translation.formatted_citation }}
              </a>
              <span v-else>
                {{ translation.formatted_citation }}
              </span>
              <span v-if="translation.range">. {{ translation.range }}</span>
            </span>
            <span v-for="note in translation.note" class="block">
              {{ note }}
            </span>
          </p>
        </template>
      </div>
    </div>

    <template v-else>
      <template v-for="translation in translations">
        <p>
          <span class="block">
            <a v-if="translation.url" :href="translation.url" target="_blank">
              {{ translation.formatted_citation }}
            </a>
            <span v-else>
              {{ translation.formatted_citation }}
            </span>
            <span v-if="translation.range">. {{ translation.range }}</span>
          </span>
          <span v-for="note in translation.note" class="block">
            {{ note }}
          </span>
        </p>
      </template>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    translations: {type: Array, required: true},
    displayLabel: {type: Boolean, required: false, default: true},
  },
};
</script>