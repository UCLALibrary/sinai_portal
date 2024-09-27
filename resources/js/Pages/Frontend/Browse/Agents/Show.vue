<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ agent.pref_name }}
        </h2>
        <p v-if="agentJson.description && agentJson.description !== ''" class="mb-4">
          {{ agentJson.description }}
        </p>
        
        <p v-if="agent.type && agent.type !== ''">
          <span class="label">Type</span>
          {{ agent.type }}
        </p>
        
        <p v-if="agentJson.ark && agentJson.ark !== ''">
          <span class="label">ARK</span>
          {{ agentJson.ark }}
        </p>
        
        <p><span class="label">URI</span>
          {{ $page.props.appUrl }}/agents/{{ agent.id }}
        </p>

        <template v-if="agentJson.refno && agentJson.refno.length > 0">
          <p v-for="ref in agentJson.refno" :key="ref.id">
            <span class="label">{{ ref.source }}</span>
            {{ ref.idno }}
          </p>
        </template>

        <h3>Personal Information</h3>
         <p v-if="agentJson.birth && agentJson.birth.value !== ''">
          <span class="label">Birth Date</span>
          {{ agentJson.birth.value }}
        </p>

        <p v-if="agentJson.death && agentJson.death.value !== ''">
          <span class="label">Death Date</span>
          {{ agentJson.death.value }}
        </p>

        <p v-if="agentJson.floruit && agentJson.floruit.value !== ''">
          <span class="label">Floruit</span>
          {{ agentJson.floruit.value }}
        </p>

        <p v-if="agentJson.gender && agentJson.gender !== ''">
          <span class="label">Gender</span>
          {{ agentJson.gender }}
        </p>

        <template v-if="agentJson.alt_name && agentJson.alt_name.length > 0">
          <h3>Name Variants</h3>
          <p>
            {{ agentJson.alt_name.join(', ') }}
          </p>
        </template>

        <template v-if="agentJson.note && agentJson.note.value !== ''">
          <h3>Notes</h3>
          <p v-for="currentNote in agentJson.note" :key="currentNote">
            <div class="label">{{ currentNote.type.label }}</div>
            <div>{{ currentNote.value }}</div>
          </p>
        </template>

        <template v-if="agent.citations && agent.citations.length > 0">
          <h3>References</h3>
          <ul>
            <li v-for="citation in agent.citations" :key="citation.id">
              {{ citation.formatted_citation }}
            </li>
          </ul>
        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ agent.pref_name }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/agents/{{ agent.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-beige border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">
        
        <template v-if="agent.related_works && agent.related_works.length > 0">
          <h3>Related Works</h3>
          <ul>
            <li v-for="relatedWork in agent.related_works" :key="relatedWork.id">
              <Link :href="route('frontend.works.show', relatedWork.id)">
                {{ relatedWork.pref_title }}
              </Link>
              <span class="ml-1" v-if="relatedWork.rel">({{ relatedWork.rel }})</span>
            </li>
          </ul>
        </template>

        <template v-if="agent.related_agents && agent.related_agents.length > 0">
          <h3>Related Agents</h3>
          <ul>
            <li v-for="relatedAgent in agent.related_agents" :key="relatedAgent.id">
              <Link :href="route('frontend.agents.show', { agent: relatedAgent.id })">
                {{ relatedAgent.pref_name }}
              </Link>
              <span class="ml-1" v-if="relatedAgent.rel">({{ relatedAgent.rel }})</span>
            </li>
          </ul>
        </template>

        <template v-if="agentJson.rel_con && agentJson.rel_con.length > 0">
          <h3>See Also</h3>
          <p v-for="rel in agentJson.rel_con" :key="rel">
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
    agent: { type: Object, required: true },
  })

  const agentJson = computed(() => {
    if (typeof props.agent.json === 'string') {
      return JSON.parse(props.agent.json);
    }
  })

  // Create reactive variables for the download URL and file name
  const downloadUrl = ref('');
  const fileName = props.agent.id + '.json';

  // Generate the download link when the component mounts
  onMounted(() => {
    const jsonString = JSON.stringify(agentJson.value, null, 2); // Pretty print the JSON
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
  
  section.sidebar h3:first-of-type {
    @apply mt-0
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
    @apply block md:inline-block text-sm uppercase font-medium w-28
  }

  ul li {
    @apply my-2
  }
</style>