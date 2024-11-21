<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">

        <div class="pb-8">
          <h2>
            {{ layer.identifier }}
          </h2>

          <p>
            <strong>{{ layerJson.state.label }} from {{ source }}, {{ layerJson.locus }}</strong>
          </p>

          <p class="italic">
            {{ layerJson.summary }}
          </p>

          <p v-if="layerJson.assoc_date && layerJson.assoc_date.some(date => date.type?.id === 'origin')">
            {{ layerJson.assoc_date.filter(date => date.type?.id === 'origin').map(date => date.value).join('; ') }}
          </p>
        </div>

        <h3>Layer Overview</h3>

        <div v-if="layerJson.ark && layerJson.ark !== ''" class="item-container">
          <span class="item-label">ARK</span>
          <p class="item-value">
            {{ layerJson.ark }}
          </p>
        </div>

        <div v-if="layerJson.extent && layerJson.extent !== ''" class="item-container">
          <span class="item-label">Extent</span>
          <p class="item-value">
            {{ layerJson.extent }}
          </p>
        </div>

        <div class="item-container">
          <span class="item-label">Primary Languages</span>
          <p class="item-value">
          </p>
        </div>

        <template v-if="layerJson.writing && layerJson.writing.length > 0">
          <h3>Writing and Hands</h3>
          <div v-for="(writing, writingIndex) in layerJson.writing" :key="writingIndex" class="mb-8">
            <p>
              {{ writing.locus }}: {{ writing.script.map(script => script.label).join(', ') }}
            </p>
            <p v-if="writing.note && writing.note.length > 0">
              {{ writing.note.join(', ') }}
            </p>
          </div>
        </template>

        <template v-if="layerJson.ink && layerJson.ink.length > 0">
          <h3>Ink</h3>
          <div v-for="(ink, index) in layerJson.ink" :key="index" class="mb-8">
            <p>
              {{ ink.locus }}<template v-if="ink.color && ink.color.length > 0">: {{ ink.color.join(', ') }}</template>
            </p>
            <template v-if="ink.note && ink.note.length > 0">
              <p v-for="(note, index) in ink.note" :key="index">
                {{ note }}
              </p>
            </template>
          </div>
        </template>

        <template v-if="layerJson.layout && layerJson.layout.length > 0">
          <h3>Page layout</h3>
          <div v-for="(layout, index) in layerJson.layout" :key="index" class="mb-8">
            <p>
              {{ layout.locus }}, {{ layout.lines }} lines, {{ layout.columns }} columns
            </p>
            <p v-if="layout.dim">
              {{ layout.dim }}
            </p>
            <template v-if="layout.note && layout.note.length > 0">
              <p v-for="(note, index) in layout.note" :key="index">
                {{ note }}
              </p>
            </template>
          </div>
        </template>

        <template v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'foliation').length > 0">
          <h3>Foliation Note</h3>
          <p v-for="(note, index) in layerJson.note.filter(note => note.type.id === 'foliation')" :key="index">
            {{ note.value }}
          </p>
        </template>

        <h3>Contents</h3>
        <template v-for="textUnit in layer.text_units">
          <p>
            <strong>{{ textUnit.label }}; {{ textUnit.locus }}</strong>
          </p>
          <div class="item-container">
            <span class="item-label">Languages</span>
            <p class="item-value">
              {{ textUnit.lang.map(lang => lang.label).join('; ') }}
            </p>
          </div>
        </template>


        <template v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'ornamentation').length > 0">
          <h3>Ornamentation</h3>
          <ul>
            <li v-for="(ornamentNote, index) in layerJson.note.filter(note => note.type.id === 'ornamentation')" :key="index">
              {{ ornamentNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="layerJson.note && (layerJson.note.filter(note => note.type.id === 'condition').length > 0 || layerJson.note.filter(note => note.type.id === 'general').length > 0)">
          <h3>Notes</h3>

          <template v-if="layerJson.note.filter(note => note.type.id === 'condition').length > 0">
            <strong>Condition</strong>
            <ul>
              <li v-for="(conditionNote, index) in layerJson.note.filter(note => note.type.id === 'condition')" :key="index">
                {{ conditionNote.value }}
              </li>
            </ul>
          </template>

          <template v-if="layerJson.note.filter(note => note.type.id === 'general').length > 0">
            <strong>General</strong>
            <ul>
              <li v-for="(generalNote, index) in layerJson.note.filter(note => note.type.id === 'general')" :key="index">
                {{ generalNote.value }}
              </li>
            </ul>
          </template>
        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ layer.identifier }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/layers/{{ layer.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">

        <template v-if="layerJson.text_unit && layerJson.text_unit.length > 0">
          <h3>Text units</h3>
          <ul>
            <li v-for="textUnit in layerJson.text_unit" >
              {{ textUnit.label }}
            </li>
          </ul>
        </template>

        <h3>Downloads</h3>
        <p>
          <a :href="downloadUrl" class="button" :download="fileName">&darr; JSON</a>
        </p>
      </section>

    </div>
  </FrontendLayout>
</template>

<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import FrontendLayout from '@/Layouts/FrontendLayout.vue'

  const props = defineProps({
    title: { type: String, required: true },
    last_modified: { type: String, required: true },
    layer: { type: Object, required: true },
    source: { type: String, required: true },
  })

  const layerJson = computed(() => {
    if (typeof props.layer.json === 'string') {
      return JSON.parse(props.layer.json);
    }
  })

  // Create reactive variables for the download URL and file name
  const downloadUrl = ref('');
  const fileName = props.layer.ark + '.json';

  // Generate the download link when the component mounts
  onMounted(() => {
    const jsonString = JSON.stringify(layerJson.value, null, 2); // Pretty print the JSON
    const blob = new Blob([jsonString], { type: 'application/json' });
    downloadUrl.value = URL.createObjectURL(blob);
  });

  // Clean up the object URL when the component is destroyed
  onBeforeUnmount(() => {
    URL.revokeObjectURL(downloadUrl.value);
  });
</script>

<style lang="postcss" scoped>
  h2 {
    @apply text-2xl xl:text-3xl pb-2
  }

  h3 {
    @apply uppercase tracking-wider font-medium text-base border-b border-gray-300 py-2 mt-8 mb-2
  }

  section.sidebar h3:first-of-type {
    @apply mt-0
  }

  p {
    @apply mb-2 max-w-2xl xl:max-w-4xl
  }

  .separator {
    @apply border-b border-gray-300 my-8
  }

  a {
    @apply text-black border-black border-b border-dotted hover:border-solid
  }

  a.button {
    @apply px-2 py-1 mt-1 rounded-full bg-white shadow border-0 hover:bg-sinai-light-blue text-sm
  }

  p {
    @apply xl:text-lg
  }

  .label {
    @apply block md:inline-block text-sm uppercase font-medium w-56
  }

  ul li {
    @apply my-2 list-disc ml-4 text-base xl:text-lg
  }

  .item-container {
    @apply flex flex-col md:flex-row
  }

  .item-label {
    @apply block md:inline-block text-sm md:leading-8 uppercase font-medium w-56
  }

  .item-value {
    @apply flex-1
  }
</style>