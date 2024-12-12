<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ textUnit.label }}
        </h2>

        <p v-if="textUnit.source && textUnit.source[0] !== ''">
          From {{ textUnit.source[0].shelfmark + (textUnit.source[0].state_label ? ` (${textUnit.source[0].state_label})` : '') + (textUnit.source[0].locus ? `, ${textUnit.source[0].locus}` : '') }}
        </p>

        <p class="italic mb-8">
          {{ textUnitJson.summary }}
        </p>

        <div v-if="textUnitJson.ark && textUnitJson.ark !== ''" class="item-container">
          <span class="item-label">Ark</span>
          <p class="item-value">
            {{ textUnitJson.ark }}
          </p>
        </div>

        <div v-if="textUnitJson.lang && textUnitJson.lang.length > 0" class="item-container">
          <span class="item-label">Languages</span>
          <p class="item-value">
            {{ textUnitJson.lang.map(lang => lang.label).join('; ') }}
          </p>
        </div>

        <NotesContents :notes="textUnitJson.note.filter(note => note.type.id === 'contents')" />
        <NotesGeneral :notes="textUnitJson.note.filter(note => note.type.id === 'general')" />

        <h3>Resources</h3>
        <ResourcesEditions :editions="textUnit.editions"/>
        <ResourcesTranslations :translations="textUnit.translations"/>
        <ResourcesReferences :references="textUnit.references"/>
        <ResourcesBibliographies :bibliographies="textUnit.bibliographies"/>

        <h3>Preferred Citation</h3>
        <p>
          "{{ textUnitJson.label }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/textunits/{{ textUnit.id }}
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
  import ResourcesEditions from "@/Pages/Frontend/Browse/Components/ResourcesEditions.vue";
  import ResourcesTranslations from '@/Pages/Frontend/Browse/Components/ResourcesTranslations.vue';
  import ResourcesReferences from '@/Pages/Frontend/Browse/Components/ResourcesReferences.vue';
  import ResourcesBibliographies from '@/Pages/Frontend/Browse/Components/ResourcesBibliographies.vue';
  import NotesGeneral from "@/Pages/Frontend/Browse/Components/NotesGeneral.vue";
  import NotesContents from "@/Pages/Frontend/Browse/Components/NotesContents.vue";


  const props = defineProps({
    title: { type: String, required: true },
    last_modified: { type: String, required: true },
    textUnit: { type: Object, required: true },
  })

  const textUnitJson = computed(() => {
    if (typeof props.textUnit.json === 'string') {
      return JSON.parse(props.textUnit.json);
    }
  })

  // Create reactive variables for the download URL and file name
  const downloadUrl = ref('');
  const fileName = props.textUnit.ark + '.json';

  // Generate the download link when the component mounts
  onMounted(() => {
    const jsonString = JSON.stringify(textUnitJson.value, null, 2); // Pretty print the JSON
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

  .separator {
    @apply border-b border-gray-300 my-8
  }

  a {
    @apply text-sinai-dark-blue
  }

  a.button {
    @apply px-2 py-1 mt-1 rounded-full bg-white shadow border-0 hover:bg-sinai-light-blue text-sm
  }


  ul li {
    @apply my-2 list-disc ml-4 text-base xl:text-lg
  }
</style>