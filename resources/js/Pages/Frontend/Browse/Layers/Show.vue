<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
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

        <div class="item-container">
          <span class="item-label">Primary Languages</span>
          <p class="item-value">
            {{ layer.primary_languages.join(', ') }}
          </p>
        </div>

        <template v-if="layerJson.writing && layerJson.writing.length > 0">
          <p class="mt-8">
            <strong>Writing and Hands</strong>
          </p>
          <div v-for="(writing, writingIndex) in layerJson.writing" :key="writingIndex" class="mb-8">
            <p>
              <template v-if="writing.locus && writing.locus !== ''">{{ writing.locus }}: </template>{{ writing.script.map(script => script.label).join(', ') }}
            </p>
            <template v-if="writing.note && writing.note.length > 0">
              <p v-for="note in writing.note">
                {{ note }}
              </p>
            </template>
          </div>
        </template>

        <template v-if="layerJson.ink && layerJson.ink.length > 0">
          <p class="mt-8">
            <strong>Ink</strong>
          </p>
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
          <p class="mt-8">
            <strong>Page layout</strong>
          </p>
          <div v-for="(layout, index) in layerJson.layout" :key="index" class="mb-8">
            <p>
              {{ layout.locus }}: {{ layout.lines }} | {{ layout.columns }}
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
          <p class="mt-8">
            <strong>Foliation Note</strong>
          </p>
          <p v-for="note in layerJson.note.filter(note => note.type.id === 'foliation')">
            {{ note.value }}
          </p>
        </template>

        <h3>Contents</h3>
        <template v-for="textUnit in layer.text_units">
          <p>
            <strong><a :href="'/textunits/' + textUnit.id">{{ textUnit.parentLabel }}</a></strong><template v-if="textUnit.locus && textUnit.locus !== ''"> ({{ textUnit.locus }})</template>
          </p>
          <p v-if="textUnit.lang && textUnit.lang.length > 0">
            Languages: {{ textUnit.lang.map(lang => lang.label).join(' | ') }}
          </p>
          <p v-if="textUnit.work_wit && textUnit.work_wit.length > 0">
            Works: {{ textUnit.work_wit.map(work => work.pref_title || work.desc_title || '').filter(title => title).join(' | ') }}
          </p>
        </template>

        <template v-if="layer.colophons && layer.colophons.length > 0">
          <h3>Colophon<template v-if="layer.colophons.length > 1">s</template></h3>
          <div v-for="para in layer.colophons" class="mb-8">
            <p>
              <strong>{{ para.locus }}, {{ para.label }}</strong>
            </p>
            <div class="indent">
              <p>
                Languages: {{ para.lang.map(lang => lang.label).join(', ') }} | Scripts: {{ para.script.map(script => script.label).join(', ') }}
              </p>
              <p>
                Transcription: {{ para.as_written }}
              </p>
              <p v-if="para.translation && para.translation.length > 0">
                Translation: {{ para.translation.join('; ') }}
              </p>
              <p class="font-medium italic" v-if="(para.assoc_name && para.assoc_name.length > 0) || (para.assoc_place && para.assoc_place.length > 0) || (para.assoc_date && para.assoc_date.length > 0)">
                Associated Names, Places, Dates
              </p>
              <template v-if="para.assoc_name && para.assoc_name.length > 0">
                <ul>
                  <template v-for="name in para.assoc_name">
                    <li class="indent">
                      {{ name.role.label }}: {{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="name.note && name.note.length > 0" class="indent">
                          {{ name.note.join('; ') }}
                        </span>
                    </li>
                  </template>
                </ul>
              </template>
              <template v-if="para.assoc_place && para.assoc_place.length > 0">
                <ul>
                  <template v-for="place in para.assoc_place">
                    <li class="indent">
                      {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="place.note && place.note.length > 0" class="indent">
                          {{ place.note.join('; ') }}
                        </span>
                    </li>
                  </template>
                </ul>
              </template>
              <template v-if="para.assoc_date && para.assoc_date.length > 0">
                <ul>
                  <template v-for="date in para.assoc_date">
                    <li class="indent">
                      {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
                        <span v-if="date.note && date.note.length > 0" class="indent">
                          {{ date.note.join('; ') }}
                        </span>
                    </li>
                  </template>
                </ul>
              </template>
              <template v-if="para.note && para.note.length > 0">
                <p class="font-medium italic">Notes</p>
                <p class="indent" v-for="note in para.note">
                  {{ note }}
                </p>
              </template>
            </div>
          </div>
        </template>

        <template v-if="layer.para_except_colophons && layer.para_except_colophons.length > 0">
          <h3>Paracontent</h3>
          <ParacontentPara :paracontents="layer.para_except_colophons" />
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

        <template v-if="(layer.associated_names_from_root && layer.associated_names_from_root.length > 0)">
          <h3>Associated Names, Places, Dates</h3>

          <div v-for="name in layer.associated_names_from_root" class="mb-8">
            <p>
              <template v-if="name.role.label">{{ name.role.label }}: </template>{{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="name.note && name.note.length > 0" class="indent">
              {{ name.note.join(", ") }}
            </p>
          </div>

          <div v-for="place in layer.associated_places_from_root" class="mb-8">
            <p>
              {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="place.note && place.note.length > 0" class="indent">
              {{ place.note.join(", ") }}
            </p>
          </div>

          <div v-for="date in layer.associated_dates_from_root" class="mb-8">
            <p>
              {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="date.note && date.note.length > 0" class="indent">
              {{ date.note.join(", ") }}
            </p>
          </div>
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

        <template v-if="layer.text_units && layer.text_units.length > 0">
          <h3>Text units</h3>
          <ul>
            <li v-for="textUnit in layer.text_units">
              <Link :href="route('frontend.textunits.show', { textunit: textUnit.id })">
                {{ textUnit.label }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="layer.works && layer.works.length > 0">
          <h3>Works</h3>
          <ul>
            <li v-for="work in layer.works">
              <Link :href="route('frontend.works.show', { work: work.id })">
                {{ work.pref_title }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="layer.all_associated_names && layer.all_associated_names.length > 0">
          <h3>Names</h3>
          <ul>
            <li v-for="name in layer.all_associated_names">
              <Link :href="route('frontend.agents.show', { agent: name.id })">
                {{ name.pref_name }}
              </Link>
            </li>
          </ul>
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

  a {
    @apply text-sinai-dark-blue
  }

  a.button {
    @apply px-2 py-1 mt-1 rounded-full bg-white shadow border-0 hover:bg-sinai-light-blue text-sm
  }

  p {
    @apply xl:text-lg
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