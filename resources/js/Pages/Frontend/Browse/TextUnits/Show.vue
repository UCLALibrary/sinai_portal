<template>
  <FrontendLayout :title="title">
    <div class="record-detail-view flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ textUnit.label }}
        </h2>

        <p v-if="textUnit.source && textUnit.source.shelfmark">
          From {{ textUnit.source.shelfmark + (textUnit.source.state_label ? ` (${textUnit.source.state_label})` : '') + (textUnit.source.locus ? `, ${textUnit.source.locus}` : '') }}
        </p>

        <OverviewSummary :summary="textUnitJson.summary" />

        <h3>Text Unit Overview</h3>
        <OverviewArk :ark="textUnitJson.ark"/>
        <OverviewLanguages :languages="textUnitJson.lang" />

        <template v-if="textUnit.work_witnesses && textUnit.work_witnesses.length > 0">
          <h3>Items</h3>
          <WorkWitnesses :work-witnesses="textUnit.work_witnesses"/>
        </template>

        <template v-if="textUnit.para && textUnit.para.length > 0">
          <h3>Paracontent</h3>
          <ParacontentPara :paracontents="textUnit.para" />
        </template>

        <template v-if="textUnitJson.note && textUnitJson.note.filter(note => note.type.id === 'para').length > 0">
          <h3>Miscellaneous Paracontent</h3>
          <NotesMisc :notes="textUnitJson.note?.filter(note => note.type.id === 'para') || []" />
        </template>

        <template v-if="textUnitJson.note && textUnitJson.note.some(note => note.type.id === 'contents' || note.type.id === 'general')">
          <h3>Notes</h3>
          <NotesContents :notes="textUnitJson.note?.filter(note => note.type.id === 'contents') || []" />
          <NotesGeneral :notes="textUnitJson.note?.filter(note => note.type.id === 'general') || []" />
        </template>

        <template v-if="hasResources">
          <h3>Resources</h3>
          <ResourcesEditions :editions="textUnit.editions"/>
          <ResourcesTranslations :translations="textUnit.translations"/>
          <ResourcesReferences :references="textUnit.references"/>
          <ResourcesBibliographies :bibliographies="textUnit.bibliographies"/>
        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ textUnitJson.label }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/textunits/{{ textUnit.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">

        <Link 
          v-if="$page.props.auth.user && pageProps.roles.permissions.includes('view cms')"
          class="flex items-center mb-4" :href="route('resources.edit', { resourceName: 'text-units', resourceId: textUnit.id })">
          <PencilSquareIcon class="inline-block w-5 h-5 mr-1" />
          Edit Text Unit
        </Link>

        <SidebarWorks :works="textUnit.sidebar_works" />
        <SidebarNames :names="textUnit.sidebar_names" />
        <SidebarTextUnits title="Reconstructions" :text-units="textUnit.sidebar_reconstructions" />
        <SidebarTextUnits title="Reconstructed From" :text-units="textUnit.sidebar_reconstructed_from" />
        <SidebarKeywordsFeatures :features="textUnitJson.features" route-name="frontend.textunits.index" />

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
  import { Link, usePage } from '@inertiajs/vue3';
  import FrontendLayout from '@/Layouts/FrontendLayout.vue'
  import ResourcesEditions from "@/Pages/Frontend/Browse/Components/ResourcesEditions.vue";
  import ResourcesTranslations from '@/Pages/Frontend/Browse/Components/ResourcesTranslations.vue';
  import ResourcesReferences from '@/Pages/Frontend/Browse/Components/ResourcesReferences.vue';
  import ResourcesBibliographies from '@/Pages/Frontend/Browse/Components/ResourcesBibliographies.vue';
  import NotesGeneral from "@/Pages/Frontend/Browse/Components/NotesGeneral.vue";
  import NotesContents from "@/Pages/Frontend/Browse/Components/NotesContents.vue";
  import ParacontentPara from "@/Pages/Frontend/Browse/Components/ParacontentPara.vue";
  import OverviewArk from "@/Pages/Frontend/Browse/Components/OverviewArk.vue";
  import OverviewLanguages from "@/Pages/Frontend/Browse/Components/OverviewLanguages.vue";
  import WorkWitnesses from "@/Pages/Frontend/Browse/Components/WorkWitnesses.vue";
  import OverviewSummary from "@/Pages/Frontend/Browse/Components/OverviewSummary.vue";
  import SidebarKeywordsFeatures from "@/Pages/Frontend/Browse/Components/SidebarKeywordsFeatures.vue";
  import SidebarNames from "@/Pages/Frontend/Browse/Components/SidebarNames.vue";
  import NotesMisc from "@/Pages/Frontend/Browse/Components/NotesMisc.vue";
  import SidebarWorks from "@/Pages/Frontend/Browse/Components/SidebarWorks.vue";
  import SidebarTextUnits from "@/Pages/Frontend/Browse/Components/SidebarTextUnits.vue";
  import { PencilSquareIcon } from '@heroicons/vue/20/solid'

  const { props: pageProps } = usePage()

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

  const hasResources = computed(() => {
    const hasEditions = props.textUnit.editions && props.textUnit.editions.length > 0;
    const hasTranslations = props.textUnit.translations && props.textUnit.translations.length > 0;
    const hasReferences = props.textUnit.references && props.textUnit.references.length > 0;
    const hasBibliographies = props.textUnit.bibliographies && props.textUnit.bibliographies.length > 0;
    return hasEditions || hasTranslations || hasReferences || hasBibliographies;
  });
</script>