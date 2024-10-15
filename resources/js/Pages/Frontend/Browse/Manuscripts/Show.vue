<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ manuscript.identifier }}
        </h2>

        <p class="pb-8">
          {{ manuscriptJson.summary }}
        </p>

        <p v-if="manuscriptJson.ark && manuscriptJson.ark !== ''">
          <span class="label">ARK</span>
          {{ manuscriptJson.ark }}
        </p>

        <p v-if="manuscriptJson.location && manuscriptJson.location.length > 0">
          <span class="label">Location</span>
          <span v-for="(location, index) in manuscriptJson.location" :key="index">
              <span v-if="index > 0" class="block"></span>
              <span v-if="index > 0" class="label"></span> {{ location.repository }}, {{ location.collection }}
            </span>
        </p>

        <p v-if="[manuscriptJson.extent, manuscriptJson.dim, manuscriptJson.weight].filter(val => val && val !== '').length">
          <span class="label">Extent</span>
          {{ [manuscriptJson.extent, manuscriptJson.dim, manuscriptJson.weight].filter(val => val && val !== '').join(', ') }}
        </p>

        <p v-if="manuscriptJson.state && manuscriptJson.state.label !== ''">
          <span class="label">State</span>
          {{ manuscriptJson.state.label }}
        </p>

        <p v-if="manuscriptJson.fol && manuscriptJson.fol !== ''">
          <span class="label">Foliation</span>
          {{ manuscriptJson.fol }}
        </p>

        <p v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'foliation').length > 0">
          <span v-for="(foliation, index) in manuscriptJson.note.filter(note => note.type.id === 'foliation')" :key="index">
            <span v-if="index >= 0" class="block"></span>
            <span v-if="index >= 0" class="label"></span> {{ foliation.value }}
          </span>
        </p>

        <p v-if="manuscriptJson.coll && manuscriptJson.coll !== ''">
          <span class="label">Collation</span>
          {{ manuscriptJson.coll }}
        </p>

        <p v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'collation').length > 0">
          <span v-for="(collation, index) in manuscriptJson.note.filter(note => note.type.id === 'collation')" :key="index">
            <span v-if="index >= 0" class="block"></span>
            <span v-if="index >= 0" class="label"></span> {{ collation.value }}
          </span>
        </p>

        <h3>Preferred Citation</h3>
        <p>
          "{{ manuscriptJson.shelfmark }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/manuscripts/{{ manuscript.id }}
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
    manuscript: { type: Object, required: true },
  })

  const manuscriptJson = computed(() => {
    if (typeof props.manuscript.json === 'string') {
      return JSON.parse(props.manuscript.json);
    }
  })

  // Create reactive variables for the download URL and file name
  const downloadUrl = ref('');
  const fileName = props.manuscript.ark + '.json';

  // Generate the download link when the component mounts
  onMounted(() => {
    const jsonString = JSON.stringify(manuscriptJson.value, null, 2); // Pretty print the JSON
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