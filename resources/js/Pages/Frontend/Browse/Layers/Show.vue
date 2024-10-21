<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ layer.identifier }}
        </h2>

        <h3>{{ layerJson.state.label }} from {{ source }}</h3>

        <p class="pb-8">
          {{ layerJson.summary }}
        </p>

        <h3>Layer Overview</h3>

        <p v-if="layerJson.ark && layerJson.ark !== ''">
          <span class="label">ARK</span>
          {{ layerJson.ark }}
        </p>

        <p v-if="layerJson.extent">
          <span class="label">Extent</span>
          {{ layerJson.extent }}
        </p>

        <h3>Writing and hands</h3>

        <p v-for="(writing, index) in layerJson.writing" :key="index">
          <div><span v-if="writing.locus">{{ writing.locus }}: </span>{{ writing.script.map(script => script.label).join(', ') }}</div>
          <div v-if="writing.note && writing.note.length > 0">{{ writing.note.join(', ') }}</div>
        </p>


        <h3>Foliation note</h3>

        <p v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'foliation').length > 0">
          <span v-for="(foliation, index) in layerJson.note.filter(note => note.type.id === 'foliation')" :key="index">
            {{ foliation.value }}
          </span>
        </p>
        
        <p v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'condition').length > 0">
          <span v-for="(condition, index) in layerJson.note.filter(note => note.type.id === 'condition')" :key="index">
            {{ condition.value }}
          </span>
        </p>

        <h3>Preferred Citation</h3>
        <p>
          "{{ layer.identifier }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/layers/{{ layer.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">
          
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
</style>