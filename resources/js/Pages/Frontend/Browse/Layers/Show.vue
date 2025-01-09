<template>
  <FrontendLayout :title="title">
    <div class="record-detail-view flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ layer.identifier }}
        </h2>

        <p>
          {{ layerJson.state.label }}<template v-if="source"> from <a :href="'/manuscripts/' + source.id">{{ source.identifier }}</a></template><template v-if="layerJson.locus && layerJson.locus !== ''">, {{ layerJson.locus }}</template>
        </p>

        <OverviewSummary :summary="layerJson.summary" />
        
        <h3>Layer Overview</h3>

        <OverviewArk :ark="layerJson.ark"/>

        <div v-if="layerJson.assoc_date && layerJson.assoc_date.some(date => date.type?.id === 'origin')" class="item-container">
          <span class="item-label">Origin Date</span>
          <p class="item-value">
            {{ layerJson.assoc_date.filter(date => date.type?.id === 'origin').map(date => date.value).join('; ') }}
          </p>
        </div>

        <div v-if="layerJson.extent && layerJson.extent !== ''" class="item-container">
          <span class="item-label">Extent</span>
          <p class="item-value">
            {{ layerJson.extent }}
          </p>
        </div>

        <OverviewLanguages :languages="layer.primary_languages || []" />

        <OverviewWritingHands :writings="layerJson.writing" />
        <OverviewInks :inks="layerJson.ink" />
        <OverviewPageLayouts :layouts="layerJson.layout" />
        <OverviewFoliationNotes :notes="layerJson.note?.filter(note => note.type.id === 'foliation') || []" />

        <template v-if="layer.text_units && layer.text_units.length > 0">
          <h3>Contents</h3>
          <ContentsTextUnits :text-units="layer.text_units" />
        </template>

        <template v-if="layer.colophons && layer.colophons.length > 0">
          <h3>Colophon<template v-if="layer.colophons.length > 1">s</template></h3>
          <ParacontentPara :paracontents="layer.colophons"/>
        </template>

        <template v-if="layer.para_except_colophons && layer.para_except_colophons.length > 0">
          <h3>Paracontent</h3>
          <ParacontentPara :paracontents="layer.para_except_colophons" :show-para-type-label="true"/>
        </template>

        <template v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'para').length > 0">
          <h3>Miscellaneous Paracontent</h3>
          <ul>
            <li v-for="paraNote in layerJson.note.filter(note => note.type.id === 'para')">
              {{ paraNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="layerJson.note && layerJson.note.filter(note => note.type.id === 'ornamentation').length > 0">
          <h3>Ornamentation</h3>
          <p v-for="(ornamentNote, index) in layerJson.note.filter(note => note.type.id === 'ornamentation')" :key="index">
            {{ ornamentNote.value }}
          </p>
        </template>

        <template v-if="layer.related_manuscripts && layer.related_manuscripts.length > 0">
          <h3>Related Manuscripts</h3>
          <div v-for="relatedMs in layer.related_manuscripts" class="mb-8">
            <p>
              <strong>
                {{ relatedMs.label }} ({{ relatedMs.type.label }})
              </strong>
            </p>
            <p class="indent">
              <template v-for="(ms, index) in relatedMs.mss">
                <template v-if="!ms.id && !ms.url">{{ ms.label}}</template>
                <a v-else :href="getManuscriptLink(ms).url" :target="getManuscriptLink(ms).isExternal ? '_blank' : '_self'">
                  {{ ms.label }}
                </a>
                <span v-if="index < relatedMs.mss.length - 1"> | </span>
              </template>
            </p>
            <template v-if="relatedMs.note && relatedMs.note.length > 0">
              <p v-for="note in relatedMs.note" class="indent">
                {{ note }}
              </p>
            </template>
          </div>
        </template>

        <template v-if="layerJson.note && (layerJson.note.filter(note => note.type.id === 'condition').length > 0 || layerJson.note.filter(note => note.type.id === 'general').length > 0)">
          <h3>Notes</h3>

          <template v-if="layerJson.note.filter(note => note.type.id === 'condition').length > 0">
            <p>
              <strong>Condition</strong>
            </p>
            <ul class="bulleted">
              <li v-for="(conditionNote, index) in layerJson.note.filter(note => note.type.id === 'condition')" :key="index">
                {{ conditionNote.value }}
              </li>
            </ul>
          </template>

          <NotesGeneral :notes="layerJson.note?.filter(note => note.type.id === 'general') || []" />

        </template>

        <template v-if="(layer.associated_names_from_root && layer.associated_names_from_root.length > 0) ||
          (layer.associated_places_from_root && layer.associated_places_from_root.length > 0) ||
          (layer.associated_dates_from_root && layer.associated_dates_from_root.length > 0)">
          <h3>Associated Names, Places, Dates</h3>

          <AssociatedNames :names="layer.associated_names_from_root" />
          <AssociatedPlaces :places="layer.associated_places_from_root" />
          <AssociatedDates :dates="layer.associated_dates_from_root" />

        </template>

        <template v-if="(layer.references && layer.references.length > 0) || (layer.bibliographies && layer.bibliographies.length > 0)">
          <h3>Resources</h3>

          <ResourcesReferences :references="layer.references"/>
          <ResourcesBibliographies :bibliographies="layer.bibliographies"/>

        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ layer.identifier }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/layers/{{ layer.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">

        <template v-if="layer.sidebar_text_units && layer.sidebar_text_units.length > 0">
          <SidebarTextUnits :textUnits="layer.sidebar_text_units" />
        </template>

        <template v-if="layer.sidebar_works && layer.sidebar_works.length > 0">
          <SidebarWorks :works="layer.sidebar_works" />
        </template>

        <template v-if="layer.sidebar_names && layer.sidebar_names.length > 0">
          <SidebarNames :names="layer.sidebar_names" />
        </template>

        <!-- <template v-if="layer.all_associated_places && layer.all_associated_places.length > 0">
          <h3>Places</h3>
          <ul>
            <li v-for="place in layer.all_associated_places">
              <Link :href="route('frontend.places.show', { place: place.id })">
                {{ place.pref_name }}
              </Link>
            </li>
          </ul>
        </template> -->

        <template v-if="(layer.reconstructed_manuscripts && layer.reconstructed_manuscripts.length > 0) ||
          (layer.reconstructed_layers && layer.reconstructed_layers.length > 0) ||
          (layer.reconstructed_from_layers && layer.reconstructed_from_layers.length > 0)">
          <h3>Reconstructions</h3>
        </template>

        <template v-if="layer.reconstructed_manuscripts && layer.reconstructed_manuscripts.length > 0">
          <p>
            <strong>Manuscripts</strong>
          </p>
          <ul>
            <li v-for="manuscript in layer.reconstructed_manuscripts">
              <Link :href="route('frontend.manuscripts.show', { manuscript: manuscript.id })">
                {{ manuscript.shelfmark }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="layer.reconstructed_layers && layer.reconstructed_layers.length > 0">
          <p>
            <strong>Layers</strong>
          </p>
          <ul>
            <li v-for="layer in layer.reconstructed_layers">
              <Link :href="route('frontend.layers.show', { layer: layer.id })">
                {{ layer.label }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="layer.reconstructed_from_layers && layer.reconstructed_from_layers.length > 0">
          <p>
            <strong>Reconstructed From</strong>
          </p>
          <ul>
            <li v-for="layer in layer.reconstructed_from_layers">
              <Link :href="route('frontend.layers.show', { layer: layer.id })">
                {{ layer.label }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="layer.related_manuscripts && layer.related_manuscripts.length > 0">
          <h3>Related Manuscripts</h3>
          <template v-for="relatedMs in layer.related_manuscripts">
            <ul>
              <li v-for="ms in relatedMs.mss">
                <template v-if="getManuscriptLink(ms).hasLink">
                  <a :href="getManuscriptLink(ms).url" :target="getManuscriptLink(ms).isExternal ? '_blank' : '_self'">
                    {{ ms.label }}
                  </a>
                </template>
                <template v-else>
                  {{ ms.label }}
                </template>
              </li>
            </ul>
          </template>
        </template>

        <template v-if="layerJson.features && layerJson.features.length > 0">
          <h3>Keywords</h3>
          <ul>
            <li v-for="keyword in layerJson.features" :key="keyword.id">
              <Link :href="`${route('frontend.layers.index')}?filters=${encodeURIComponent(JSON.stringify(['features:' + keyword.label]))}`">
                {{ keyword.label }}
              </Link>
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
  import { getManuscriptLink } from '@/Shared/detailPageHelpers.js';
  import ResourcesReferences from "@/Pages/Frontend/Browse/Components/ResourcesReferences.vue";
  import ResourcesBibliographies from "@/Pages/Frontend/Browse/Components/ResourcesBibliographies.vue";
  import NotesGeneral from "@/Pages/Frontend/Browse/Components/NotesGeneral.vue";
  import ParacontentPara from "@/Pages/Frontend/Browse/Components/ParacontentPara.vue";
  import OverviewArk from "@/Pages/Frontend/Browse/Components/OverviewArk.vue";
  import OverviewSummary from "@/Pages/Frontend/Browse/Components/OverviewSummary.vue";
  import AssociatedNames from "@/Pages/Frontend/Browse/Components/AssociatedNames.vue";
  import AssociatedPlaces from "@/Pages/Frontend/Browse/Components/AssociatedPlaces.vue";
  import AssociatedDates from "@/Pages/Frontend/Browse/Components/AssociatedDates.vue";
  import OverviewWritingHands from "@/Pages/Frontend/Browse/Components/OverviewWritingHands.vue";
  import OverviewInks from "@/Pages/Frontend/Browse/Components/OverviewInks.vue";
  import OverviewPageLayouts from "@/Pages/Frontend/Browse/Components/OverviewPageLayouts.vue";
  import OverviewFoliationNotes from "@/Pages/Frontend/Browse/Components/OverviewFoliationNotes.vue";
  import SidebarNames from "@/Pages/Frontend/Browse/Components/SidebarNames.vue";
  import ContentsTextUnits from "@/Pages/Frontend/Browse/Components/ContentsTextUnits.vue";
  import SidebarTextUnits from "@/Pages/Frontend/Browse/Components/SidebarTextUnits.vue";
  import OverviewLanguages from "@/Pages/Frontend/Browse/Components/OverviewLanguages.vue";
  import SidebarWorks from "@/Pages/Frontend/Browse/Components/SidebarWorks.vue";

  const props = defineProps({
    title: { type: String, required: true },
    last_modified: { type: String, required: true },
    layer: { type: Object, required: true },
    source: { type: Object, required: true },
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