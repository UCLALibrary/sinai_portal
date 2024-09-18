<template>
  <FrontendLayout :title="title">
    <h2 class="font-dosis flex mx-auto text-3xl border-b pb-2 mb-4">
      {{ agent.pref_name }}
    </h2>
    <div class="mx-auto flex">

      <section class="w-3/4 pr-8 min-h-screen">
        <p v-if="agentJson.description && agentJson.description !== ''" class="mb-4">
          {{ agentJson.description }}
        </p>
        
        <p v-if="agent.type && agent.type !== ''">
          <span class="label">Type:</span>
          {{ agent.type }}
        </p>
        
        <p v-if="agentJson.ark && agentJson.ark !== ''">
          <span class="label">ARK:</span>
          {{ agentJson.ark }}
        </p>
        
        <p><span class="label">URI:</span>
          {{ $page.props.appUrl }}/agents/{{ agent.id }}
        </p>

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
          <span class="label">Gender:</span>
          {{ agentJson.gender }}
        </p>

        <template v-if="agentJson.alt_name && agentJson.alt_name.length > 0">
          <h3>Name Variants</h3>
          <p>
            {{ agentJson.alt_name.join(', ') }}
          </p>
        </template>

        <h3>Preferred Citation</h3>
        "{{ agent.pref_name }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
        {{ $page.props.appUrl }}/agents/{{ agent.id }}

      </section>

      <section class="w-1/4 border p-4 min-h-screen">
        <template v-if="agentJson.rel_con && agentJson.rel_con.length > 0">
          <h3>See Also</h3>
          <p v-for="rel in agentJson.rel_con" :key="rel">
            <a :href="rel.uri">{{ rel.source }}</a>
          </p>
        </template>
  
        <h3>Downloads</h3>
        <p>
          <a :href="downloadUrl" :download="fileName">JSON</a>
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
  h3 {
    @apply mt-4 mb-2 font-bold text-xl
  }
  p {
    @apply mb-2  
  }

  a {
    @apply text-blue-600
  }

  .label {
    @apply font-bold
  }
</style>