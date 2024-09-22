<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ work.pref_title }}
        </h2>
        <p v-if="workJson.desc && workJson.desc !== ''" class="mb-4">
          {{ workJson.desc }}
        </p>
        
        <p v-if="workJson.ark && workJson.ark !== ''">
          <span class="label">ARK</span>
          {{ workJson.ark }}
        </p>
        
        <p><span class="label">URI</span>
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
        
        <p v-if="work.authors && work.authors.length > 0">
          <span class="label">Author</span>
          {{ work.authors.map(author => author.pref_name).join('; ') }}
        </p>

        <p v-if="workJson.creation && workJson.creation.value && workJson.creation.value !== ''">
          <span class="label">Creation Date</span>
          {{ workJson.creation.value }}
        </p>

        
        <p v-if="workJson.genre && workJson.genre.length > 0">
          <span class="label">Genre</span>
          {{ workJson.genre.join('; ') }}
        </p>
      

        <template v-if="workJson.incipit && workJson.incipit.value != ''">
          <h3>Incipit</h3>
          <p v-if="workJson.incipit && workJson.incipit.value && workJson.incipit.value !== ''">
            <span class="label">Incipit</span>
            {{ workJson.incipit.value }}
          </p>
  
          <p v-if="workJson.incipit && workJson.incipit.translation.length > 0">
            <span class="label">Translation</span>
            {{ workJson.incipit.translation.join('; ') }}
          </p>
  
          <p v-if="workJson.incipit && workJson.incipit.source && workJson.incipit.source !== ''">
            <span class="label">Source</span>
            <span class="text-sm">{{ workJson.incipit.source.join('; ') }}</span>
          </p>
        </template>

         <template v-if="workJson.explicit && workJson.explicit.value != ''">
           <h3>Explicit</h3>
           <p v-if="workJson.explicit && workJson.explicit.value && workJson.explicit.value !== ''">
            <span class="label">Explicit</span>
            {{ workJson.explicit.value }}
          </p>
  
          <p v-if="workJson.explicit && workJson.explicit.translation.length > 0">
            <span class="label">Translation</span>
            {{ workJson.explicit.translation.join('; ') }}
          </p>
  
          <p v-if="workJson.explicit && workJson.explicit.source && workJson.explicit.source !== ''">
            <span class="label">Source</span>
            <span class="text-sm">{{ workJson.explicit.source.join('; ') }}</span>
          </p>
         </template>

        <template v-if="workJson.alt_title && workJson.alt_title.length > 0">
          <h3>Title Variants</h3>
          <p v-for="alt_title in workJson.alt_title" :key="alt_title">
            {{ alt_title }}
          </p>
        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ work.pref_title }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/works/{{ work.id }}
        </p>

      </section>

      <section class="w-full h-auto lg:w-1/4 border-sinai-beige border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">
        <template v-if="workJson.rel_con && workJson.rel_con.length > 0">
          <h3 class="mt-0">See Also</h3>
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
    @apply text-2xl xl:text-3xl pb-8
  }

  h3 {
    @apply uppercase tracking-wider font-medium text-base border-b border-gray-300 py-2 mt-8 mb-2
  }
  
  p {
    @apply mb-2 max-w-2xl xl:max-w-4xl
  }

  a {
    @apply text-black border-black border-b border-dotted hover:border-solid
  }

  a.button {
    @apply px-2 py-1 mt-1 rounded-full bg-white shadow border-0 hover:bg-sinai-beige text-sm
  }

  p {
    @apply xl:text-lg
  }

  .label {
    @apply inline-block text-sm uppercase font-medium w-56
  }
</style>