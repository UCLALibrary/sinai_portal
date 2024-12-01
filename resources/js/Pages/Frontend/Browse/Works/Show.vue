<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ work.pref_title }}
        </h2>

        <p v-if="workJson.desc && workJson.desc !== ''" class="mb-8 italic">
          {{ workJson.desc }}
        </p>
        
        <p v-if="workJson.ark && workJson.ark !== ''">
          <span class="label">ARK</span>
          {{ workJson.ark }}
        </p>
        
        <p>
          <span class="label">URI</span>
          {{ $page.props.appUrl }}/works/{{ work.id }}
        </p>

        <template v-if="workJson.refno && workJson.refno.length > 0">
          <p v-for="ref in workJson.refno" :key="ref.id">
            <span class="label">{{ ref.source }}</span>
            {{ ref.idno }}
          </p>
        </template>

        <div class="mt-8"></div>

        <p v-if="workJson.orig_lang && workJson.orig_lang.label && workJson.orig_lang.label !== ''">
          <span class="label">Original Language</span>
          {{ workJson.orig_lang.label }}
        </p>

        <p v-if="workJson.orig_lang_title && workJson.orig_lang_title !== ''">
          <span class="label">Original Language Title</span>
          {{ workJson.orig_lang_title }}
        </p>
        
        <template v-if="work.creators && work.creators.length > 0">
          <p v-for="creator in work.creators" :key="creator.id">
            <span class="label">{{ creator.role_label }}</span>
            {{ creator.pref_name }}
          </p>
        </template>

        <p v-if="workJson.creation && workJson.creation.value && workJson.creation.value !== ''">
          <span class="label">Creation Date</span>
          {{ workJson.creation.value }}
        </p>

        <p v-if="workJson.genre && workJson.genre.length > 0">
          <span class="label">Genre</span>
          {{ workJson.genre.map(genre => genre.label).join('; ') }}
        </p>
      
        <template v-if="workJson.incipit && workJson.incipit.value != ''">
          <div class="separator"></div>
          <p v-if="workJson.incipit && workJson.incipit.value && workJson.incipit.value !== ''">
            <span class="label">Incipit</span>{{ workJson.incipit.value }}
          </p>
  
          <p v-if="workJson.incipit && workJson.incipit.translation.length > 0">
            <span class="label">Translation</span>
            <span v-for="(translation, index) in workJson.incipit.translation" :key="index">
              <span v-if="index > 0" class="block"></span>
              <span v-if="index > 0" class="label"></span>{{ translation }}
            </span>
          </p>
  
          <p v-if="workJson.incipit && workJson.incipit.source && workJson.incipit.source !== ''">
            <span class="label">Source</span>
            <span class="text-base">{{ workJson.incipit.source.join('; ') }}</span>
          </p>
        </template>

        <template v-if="workJson.explicit && workJson.explicit.value != ''">
          <div class="separator"></div>
          <p v-if="workJson.explicit && workJson.explicit.value && workJson.explicit.value !== ''">
            <span class="label">Explicit</span>{{ workJson.explicit.value }}
          </p>
  
          <p v-if="workJson.explicit && workJson.explicit.translation.length > 0">
            <span class="label">Translation</span>
            <span v-for="(translation, index) in workJson.explicit.translation" :key="index">
              <span v-if="index > 0" class="block"></span>
              <span v-if="index > 0" class="label"></span>{{ translation }}
            </span>
          </p>
  
          <p v-if="workJson.explicit && workJson.explicit.source && workJson.explicit.source !== ''">
            <span class="label">Source</span>
            <span class="text-base">{{ workJson.explicit.source.join('; ') }}</span>
          </p>
         </template>

        <template v-if="workJson.alt_title && workJson.alt_title.length > 0">
          <h3>Title Variants</h3>
          <ul v-for="alt_title in workJson.alt_title" :key="alt_title">
            <li>{{ alt_title }}</li>
          </ul>
        </template>

        <template v-if="work.attested_titles && work.attested_titles.length > 0">
          <h3>Attested Titles</h3>
          <ul v-for="attested_title in work.attested_titles" :key="attested_title">
            <li>{{ attested_title }}</li>
          </ul>
        </template>

        <template v-if="workJson.note && workJson.note.value !== ''">
          <h3>Notes</h3>
          <ul v-for="currentNote in workJson.note" :key="currentNote">
            <li>{{ currentNote }}</li>
          </ul>
        </template>

        <template v-if="work.editions && work.editions.length > 0">
          <h3>Editions</h3>
          <ul>
            <li v-for="edition in work.editions" :key="edition.id">
              {{ edition.formatted_citation }}<span v-if="edition.range">, {{ edition.range }}.</span>
            </li>
          </ul>
        </template>

        <template v-if="work.translations && work.translations.length > 0">
          <h3>Modern Translations</h3>
          <ul>
            <li v-for="translation in work.translations" :key="translation.id">
              {{ translation.formatted_citation }}<span v-if="translation.range">, {{ translation.range }}.</span>
            </li>
          </ul>
        </template>

        <template v-if="work.citations && work.citations.length > 0">
          <h3>References</h3>
          <ul>
            <li v-for="citation in work.citations" :key="citation.id">
              {{ citation.formatted_citation }}<span v-if="citation.range">, {{ citation.range }}.</span>
            </li>
          </ul>
        </template>        

        <h3>Preferred Citation</h3>
        <p>
          "{{ work.pref_title }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/works/{{ work.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">

        <h3>Related Records</h3>
        <p>
          <Link :href="`${route('frontend.textunits.index')}?filters=${encodeURIComponent(JSON.stringify(['works:' + work.pref_title]))}`">
            Related Text Units
          </Link>
        </p>
        
        <template v-if="work.related_works && work.related_works.length > 0">
          <h3>Related Works</h3>
          <ul>
            <li v-for="relatedWork in work.related_works" :key="relatedWork.id">
              <Link :href="route('frontend.works.show', relatedWork.id)">
                {{ relatedWork.pref_title }}
              </Link>
              <span class="ml-1" v-if="relatedWork.rel">({{ relatedWork.rel.map(rel => rel.label).join('; ') }})</span>
            </li>
          </ul>
        </template>

        <template v-if="work.related_agents && work.related_agents.length > 0">
          <h3>Related Agents</h3>
          <ul>
            <li v-for="creator in work.creators" :key="creator.id">
              <Link :href="route('frontend.agents.show', { agent: creator.id })">
                {{ creator.pref_name }}
              </Link>
              <span class="ml-1" v-if="creator.role_label">({{ creator.role_label }})</span>
            </li>
            <li v-for="relatedAgent in work.related_agents" :key="relatedAgent.id">
              <Link :href="route('frontend.agents.show', { agent: relatedAgent.id })">
                {{ relatedAgent.pref_name }}
              </Link>
              <span class="ml-1" v-if="relatedAgent.rel">({{ relatedAgent.rel.map(rel => rel.label).join('; ') }})</span>
            </li>
          </ul>
        </template>

        <template v-if="workJson.rel_con && workJson.rel_con.length > 0">
          <h3>See Also</h3>
          <p v-for="rel in workJson.rel_con" :key="rel">
            <a :href="rel.uri" class="button">&rarr; {{ rel.source }}</a>
          </p>
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
    work: { type: Object, required: true },
  })

  const workJson = computed(() => {
    if (typeof props.work.json === 'string') {
      return JSON.parse(props.work.json);
    }
  })

  // Create reactive variables for the download URL and file name
  const downloadUrl = ref('');
  const fileName = props.work.id + '.json';

  // Generate the download link when the component mounts
  onMounted(() => {
    const jsonString = JSON.stringify(workJson.value, null, 2); // Pretty print the JSON
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
    @apply text-sinai-dark-blue
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