<template>
  <FrontendLayout :title="title">
    <div class="flex flex-col lg:flex-row gap-y-8 bg-white p-4 xl:p-8">
      <section class="w-full lg:w-3/4 lg:pr-16">
        <h2>
          {{ manuscript.identifier }}
          <template v-if="manuscriptJson.reconstruction !== null && manuscriptJson.reconstruction === true">
            <template v-if="manuscriptJson.type && manuscriptJson.type.label !== ''">
              ({{ manuscriptJson.type.label }})
            </template>
          </template>
        </h2>

        <OverviewSummary :summary="manuscriptJson.summary" :marginBottom="false"/>

        <p class="mb-8">
          {{ manuscript.assoc_dates_overview.join('; ') }}
        </p>

        <h3>Manuscript Overview</h3>
        <OverviewArk :ark="manuscriptJson.ark"/>

        <div v-if="shelfmarkVariants && shelfmarkVariants !== ''" class="item-container">
          <span class="item-label">Shelfmark variants</span>
          <p class="item-value">
            {{ shelfmarkVariants }}
          </p>
        </div>

        <div v-if="manuscriptJson.location && manuscriptJson.location.length > 0" class="item-container">
          <span class="item-label">Location</span>
          <p class="item-value">
            <span v-for="(location) in manuscriptJson.location" class="d-block">
              {{ location.repository }}, {{ location.collection }}
            </span>
          </p>
        </div>

        <div v-if="[manuscriptJson.extent, manuscriptJson.dim, manuscriptJson.weight].filter(val => val && val !== '').length" class="item-container">
          <span class="item-label">Extent</span>
          <p class="item-value">
            {{ [manuscriptJson.extent, manuscriptJson.dim, manuscriptJson.weight].filter(val => val && val !== '').join(' | ') }}
          </p>
        </div>

        <div v-if="manuscriptJson.state && manuscriptJson.state.label !== ''" class="item-container">
          <span class="item-label">State</span>
          <p class="item-value">
            {{ manuscriptJson.state.label }}
          </p>
        </div>

        <div v-if="manuscriptJson.fol && manuscriptJson.fol !== ''" class="item-container">
          <span class="item-label">Foliation</span>
          <p class="item-value">
            <span class="d-block">{{ manuscriptJson.fol }}</span>
            <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'foliation').length > 0">
              <span class="d-block" v-for="(foliation) in manuscriptJson.note.filter(note => note.type.id === 'foliation')">
                {{ foliation.value }}
              </span>
            </template>
          </p>
        </div>

        <div v-if="manuscriptJson.coll && manuscriptJson.coll !== ''" class="item-container">
          <span class="item-label">Collation</span>
          <p class="item-value">
            <span class="d-block">{{ manuscriptJson.coll }}</span>
            <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'collation').length > 0">
              <span class="d-block" v-for="(collation) in manuscriptJson.note.filter(note => note.type.id === 'collation')">
                {{ collation.value }}
              </span>
            </template>
          </p>
        </div>

        <div v-if="manuscript.related_overtext_layers && manuscript.related_overtext_layers.length > 0" class="item-container">
          <span class="item-label">Primary languages</span>
          <p class="item-value">
            {{ primaryLanguage }}
          </p>
        </div>

        <div v-if="manuscript.related_overtext_layers && manuscript.related_overtext_layers.length > 0" class="item-container">
          <span class="item-label">Scripts</span>
          <p class="item-value">
            {{ scriptLabels }}
          </p>
        </div>

        <template v-if="manuscript.part && manuscript.part.length > 0">
          <h3>Parts</h3>

          <template v-for="(part) in manuscript.part">
            <div class="mb-14">
              <p class="mb-0">
                <strong>{{ part.label }}</strong><template v-if="part.locus">, {{ part.locus }}</template>
              </p>

              <p v-if="part.summary && part.summary !== ''" class="italic">
                {{ part.summary }}
              </p>

              <div v-if="part.support && part.support.length > 0"class="item-container">
                <span class="item-label">Support</span>
                <p class="item-value">
                  <span class="d-block">{{ part.support.map(support => support.label).join(', ') }}</span>
                  <span class="d-block" v-if="part.note && part.note.filter(note => note.type.id === 'support').length > 0">
                    {{ part.note.filter(note => note.type.id === 'support').map(note => note.value).join(', ') }}
                  </span>
                </p>
              </div>

              <div v-if="(part.extent && part.extent !== '') || (part.dim && part.dim !== '') " class="item-container">
                <span class="item-label">Extent</span>
                <p class="d-block">{{ [part.extent, part.dim].filter(Boolean).join(' | ') }}</p>
              </div>

              <div v-if="part.note && part.note.filter(note => note.type.id === 'foliation').length > 0" class="item-container">
                <span class="item-label">Foliation</span>
                <p class="item-value">
                  {{ part.note.filter(note => note.type.id === 'foliation').map(note => note.value).join(', ') }}
                </p>
              </div>

              <div v-if="part.note && part.note.filter(note => note.type.id === 'collation').length > 0" class="item-container">
                <span class="item-label">Collation</span>
                <p class="item-value">
                  {{ part.note.filter(note => note.type.id === 'collation').map(note => note.value).join(', ') }}
                </p>
              </div>

              <template v-if="part.layer && part.layer.length > 0">
                <p>
                  <strong>Contents</strong>
                </p>
                <p v-for="layer in part.layer" :key="layer.id" class="mb-8">
                  {{ layer.parentLabel }}<template v-if="layer.parentLocus && layer.parentLocus !== ''">, {{ layer.parentLocus }}</template>
                  <span v-if="layer.assoc_date && layer.assoc_date.length > 0" class="block">
                    Origin: {{ layer.assoc_date.find(date => date.type.id === 'origin')?.value || '' }}<span v-if="layer.assoc_place && layer.assoc_place.length > 0">. {{ layer.assoc_place.find(place => place.event.id === 'origin')?.pref_name || ''  }}</span>
                  </span>
                  <span v-if="layer.text_units && layer.text_units.length > 0 || layer.writing && layer.writing.length > 0" class="block">
                    Languages: {{ layer.text_units.map(unit => unit.lang.map(lang => lang.label).join(', ')).join('; ') }} | Scripts: {{layer.writing.map(writing => writing.script.map(script => script.label).join(', ')).join('; ') }}
                  </span>
                </p>
              </template>

            </div>
          </template>
        </template>

        <template v-if="hasUndertextObjects">
          <h3>Undertext Objects</h3>

          <template v-if="manuscript.part_layer_undertext && manuscript.part_layer_undertext.length > 0">
            <p v-for="layer in manuscript.part_layer_undertext" :key="layer.id" class="mb-8">
              {{ layer.parentLabel }}<template v-if="layer.label && layer.label !== ''">, {{ layer.label }}</template>, {{ layer.parentLocus }}
              <span v-if="layer.assoc_date && layer.assoc_date.length > 0" class="block">
                {{ layer.assoc_date.find(date => date.type.id === 'origin')?.value || '' }}<span v-if="layer.assoc_place && layer.assoc_place.length > 0">. {{ layer.assoc_place.find(place => place.event.id === 'origin')?.pref_name || ''  }}</span>
              </span>
              <span v-if="layer.text_units && layer.text_units.length > 0 || layer.writing && layer.writing.length > 0" class="block">
                Languages: {{ layer.text_units.map(unit => unit.lang.map(lang => lang.label).join(', ')).join('; ') }} | Scripts: {{layer.writing.map(writing => writing.script.map(script => script.label).join(', ')).join('; ') }}
              </span>
            </p>
          </template>

          <template v-if="manuscript.layer_undertext && manuscript.layer_undertext.length > 0">
            <p v-for="layer in manuscript.layer_undertext" :key="layer.id" class="mb-8">
              {{ layer.parentLabel }}<template v-if="layer.parentLocus && layer.parentLocus !== ''">, {{ layer.parentLocus }}</template>
              <span v-if="layer.assoc_date && layer.assoc_date.length > 0" class="block">
                {{ layer.assoc_date.find(date => date.type.id === 'origin')?.value || '' }}<span v-if="layer.assoc_place && layer.assoc_place.length > 0">. {{ layer.assoc_place.find(place => place.event.id === 'origin')?.pref_name || ''  }}</span>
              </span>
              <span v-if="layer.text_units && layer.text_units.length > 0 || layer.writing && layer.writing.length > 0" class="block">
                Languages: {{ layer.text_units.map(unit => unit.lang.map(lang => lang.label).join(', ')).join('; ') }} | Scripts: {{layer.writing.map(writing => writing.script.map(script => script.label).join(', ')).join('; ') }}
              </span>
            </p>
          </template>

        </template>

        <template v-if="hasGuestContent">
          <h3>Guest Content</h3>

          <template v-if="manuscript.part_layer_guest && manuscript.part_layer_guest.length > 0">
            <p v-for="layer in manuscript.part_layer_guest" :key="layer.id" class="mb-8">
              {{ layer.parentLabel }}, {{ layer.label }}, {{ layer.parentLocus }}
              <span v-if="layer.assoc_date && layer.assoc_date.length > 0" class="block">
                {{ layer.assoc_date.find(date => date.type.id === 'origin')?.value || '' }}<span v-if="layer.assoc_place && layer.assoc_place.length > 0">. {{ layer.assoc_place.find(place => place.event.id === 'origin')?.pref_name || ''  }}</span>
              </span>
              <span v-if="layer.text_units && layer.text_units.length > 0 || layer.writing && layer.writing.length > 0" class="block">
                Languages: {{ layer.text_units.map(unit => unit.lang.map(lang => lang.label).join(', ')).join('; ') }} | Scripts: {{layer.writing.map(writing => writing.script.map(script => script.label).join(', ')).join('; ') }}
              </span>
            </p>
          </template>

          <template v-if="manuscript.layer_guest && manuscript.layer_guest.length > 0">
            <p v-for="layer in manuscript.layer_guest" :key="layer.id" class="mb-8">
              {{ layer.parentLabel }}<template v-if="layer.parentLocus && layer.parentLocus !== ''">, {{ layer.parentLocus }}</template>
              <span v-if="layer.assoc_date && layer.assoc_date.length > 0" class="block">
                {{ layer.assoc_date.find(date => date.type.id === 'origin')?.value || '' }}<span v-if="layer.assoc_place && layer.assoc_place.length > 0">. {{ layer.assoc_place.find(place => place.event.id === 'origin')?.pref_name || ''  }}</span>
              </span>
              <span v-if="layer.text_units && layer.text_units.length > 0 || layer.writing && layer.writing.length > 0" class="block">
                Languages: {{ layer.text_units.map(unit => unit.lang.map(lang => lang.label).join(', ')).join('; ') }} | Scripts: {{layer.writing.map(writing => writing.script.map(script => script.label).join(', ')).join('; ') }}
              </span>
            </p>
          </template>

        </template>

        <template v-if="hasParaGuestContent">
          <h3>Paracontent</h3>

          <template v-if="manuscript.part_para && manuscript.part_para.length > 0">
            <template v-for="part in manuscript.part_para">
              <template v-if="part.para && part.para.length > 0">
                <div v-for="para in part.para" class="mb-8">
                  <p>
                    <strong>{{ part.label }}, {{ para.locus }}, {{ para.label }} ({{ para.type.label }})</strong>
                  </p>
                  <p v-if="para.lang && para.lang.length > 0" class="indent">
                    Languages: {{ para.lang.map(lang => lang.label).join(', ') }} | Scripts: {{ para.script.map(script => script.label).join(', ') }}
                  </p>
                  <p v-if="para.as_written" class="indent">
                    Transcription: {{ para.as_written }}
                  </p>
                  <template v-if="para.translation && para.translation.length > 0">
                    <p v-for="translation in para.translation" class="indent">
                      Translation: {{ translation }}
                    </p>
                  </template>

                  <p v-if="(para.assoc_name && para.assoc_name.length > 0) || (para.assoc_place && para.assoc_place.length > 0) || (para.assoc_date && para.assoc_date.length > 0)" class="indent font-medium italic">
                    Associated Names, Places, Dates
                  </p>

                  <div v-if="para.assoc_name && para.assoc_name.length > 0">
                    <ul class="indent">
                      <li v-for="name in para.assoc_name" class="indent">
                        {{ name.role.label }}: {{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="name.note && name.note.length > 0" class="indent">
                          {{ name.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_place && para.assoc_place.length > 0">
                    <ul class="indent">
                      <li v-for="place in para.assoc_place" class="indent">
                        {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="place.note && place.note.length > 0" class="indent">
                          {{ place.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_date && para.assoc_date.length > 0">
                    <ul class="indent">
                      <li v-for="date in para.assoc_date" class="indent">
                        {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
                        <span v-if="date.note && date.note.length > 0" class="indent">
                          {{ date.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <template v-if="para.note && para.note.length > 0">
                    <div class="indent">
                      <p class="font-medium italic">
                        Notes
                      </p>
                      <p class="indent">
                        {{ para.note.join('; ') }}
                      </p>
                    </div>
                  </template>
                </div>
              </template>
            </template>
          </template>

          <ParacontentPara :paracontents="manuscript.para" />
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'para').length > 0">
          <h3>Miscellaneous Paracontent</h3>
          <ul>
            <li v-for="paraNote in manuscriptJson.note.filter(note => note.type.id === 'para')">
              {{ paraNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'ornamentation').length > 0">
          <h3>Ornamentation</h3>
          <ul>
            <li v-for="(ornamentNote) in manuscriptJson.note.filter(note => note.type.id === 'ornamentation')">
              {{ ornamentNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="allRelatedMss.length > 0">
          <h3>Related Manuscripts</h3>
          <div v-for="relatedMss in allRelatedMss" :key="relatedMss.label" class="mb-8">
            <p>
              <strong>
                {{ relatedMss.label }} ({{ relatedMss.type.label }})
              </strong>
            </p>
            <p class="indent">
              <template v-for="(ms, index) in relatedMss.mss">
                <a :href="getManuscriptLink(ms).url" :target="getManuscriptLink(ms).isExternal ? '_blank' : '_self'">
                  {{ ms.label }}
                </a>
                <span v-if="index < relatedMss.mss.length - 1"> | </span>
              </template>
            </p>
            <template v-if="relatedMss.note && relatedMss.note.length > 0">
              <p v-for="note in relatedMss.note" class="indent">
                {{ note }}
              </p>
            </template>
          </div>
        </template>

        <template v-if="manuscriptJson.note &&
          (manuscriptJson.note.filter(note => note.type.id === 'binding').length > 0 ||
          manuscriptJson.note.filter(note => note.type.id === 'provenance').length > 0 ||
          manuscriptJson.note.filter(note => note.type.id === 'condition').length > 0 ||
          manuscriptJson.note.filter(note => note.type.id === 'general').length > 0)">
          <h3>Notes</h3>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'binding').length > 0">
          <p>
            <strong>Binding</strong>
          </p>
          <ul class="bulleted">
            <li v-for="(bindingNote) in manuscriptJson.note.filter(note => note.type.id === 'binding')" class="list-disc">
              {{ bindingNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'provenance').length > 0">
          <p>
            <strong>Provenance</strong>
          </p>
          <ul class="bulleted">
            <li v-for="(provenanceNote) in manuscriptJson.note.filter(note => note.type.id === 'provenance')">
              {{ provenanceNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'condition').length > 0">
          <p>
            <strong>Condition</strong>
          </p>
          <ul class="bulleted">
            <li v-for="(conditionNote) in manuscriptJson.note.filter(note => note.type.id === 'condition')">
              {{ conditionNote.value }}
            </li>
          </ul>
        </template>

        <NotesGeneral :notes="manuscriptJson.note?.filter(note => note.type.id === 'general') || []" />

        <template v-if="(manuscript.assoc_names_from_para && manuscript.assoc_names_from_para.length > 0) ||
          (manuscriptJson.assoc_date && manuscriptJson.assoc_date.length > 0)">
          <h3>Associated Names, Places, Dates</h3>

          <div v-for="assocName in manuscript.assoc_names_from_para" class="mb-8">
            <p>
              <template v-if="assocName.role_label">{{ assocName.role_label }}: </template>{{ [assocName.as_written, assocName.pref_name].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="assocName.note" class="indent">
              {{ JSON.parse(assocName.note).join(', ') }}
            </p>
          </div>

          <!--
            <div v-for="relatedPlace in manuscript.related_places" class="mb-8">
              <p>
                {{ relatedPlace.event.label }}: {{ [relatedPlace.as_written, relatedPlace.pref_name].filter(Boolean).join(' | ') }}
              </p>
              <p v-if="relatedPlace.note && relatedPlace.note.length > 0" class="indent">
                {{ relatedPlace.note.join(", ") }}
              </p>
            </div>
          -->

          <AssociatedDates :dates="manuscriptJson.assoc_date" />
        </template>

        <template v-if="(manuscript.references && manuscript.references.length > 0) ||
          (manuscript.bibliographies && manuscript.bibliographies.length > 0) ||
          (manuscript.related_digital_versions && manuscript.related_digital_versions.length > 0) ||
          (manuscript.viscodex && manuscript.viscodex.length > 0)">
          <h3>Resources</h3>
          <ResourcesReferences :references="manuscript.references"/>
          <ResourcesBibliographies :bibliographies="manuscript.bibliographies"/>
  
          <div v-if="manuscript.related_digital_versions && manuscript.related_digital_versions.length > 0" class="item-container">
            <span class="item-label">Other Digital Versions</span>
            <div class="item-value">
              <template v-for="digVersion in manuscript.related_digital_versions">
                <p>
                  <a v-if="digVersion.url" :href="digVersion.url" target="_blank">
                    {{ digVersion.short_title }}
                  </a>
                  <span v-else>
                  {{ digVersion.short_title }}
                </span>
                  <span v-if="digVersion.alt_shelf">. {{ digVersion.alt_shelf }}</span>
                </p>
              </template>
            </div>
          </div>
  
          <div v-if="manuscriptJson.viscodex && manuscriptJson.viscodex.length > 0" class="item-container">
            <span class="item-label">Viscodex</span>
            <div class="item-value">
              <p v-for="viscodex in manuscriptJson.viscodex.filter(viscodex => viscodex.type.id === 'manuscript')">
                <a :href="viscodex.url" target="_blank">{{ viscodex.label }} ({{ viscodex.type.label }})</a>
              </p>
              <p v-for="viscodex in manuscriptJson.viscodex.filter(viscodex => viscodex.type.id !== 'manuscript')">
                <a :href="viscodex.url" target="_blank">{{ viscodex.label }} ({{ viscodex.type.label }})</a>
              </p>
            </div>
          </div>
        </template>

        <h3>Preferred Citation</h3>
        <p>
          "{{ manuscriptJson.shelfmark }}". Sinai Manuscripts Data Portal. Last modified: {{ last_modified }}.
          {{ $page.props.appUrl }}/manuscripts/{{ manuscript.id }}
        </p>

      </section>

      <section class="sidebar w-full h-auto lg:w-1/4 border-sinai-light-blue border-t-4 lg:border-t-0 lg:border-l-4 max-lg:pt-8 lg:pl-8">

        <template v-if="allInscribedLayers.length > 0">
          <h3>Inscribed Layers</h3>
          <ul>
            <li v-for="layer in allInscribedLayers">
              <Link :href="route('frontend.layers.show', { layer: layer.id.split('/').pop() })">
                {{ layer.label }} ({{ layer.type.label }})
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="manuscript.related_text_units && manuscript.related_text_units.length > 0">
          <h3>Text Units</h3>
          <ul>
            <template v-for="layer in manuscript.related_text_units">
              <li v-for="textUnit in layer.text_units">
                <Link :href="route('frontend.textunits.show', { textunit: textUnit.id })">
                  {{ textUnit.label }}
                </Link>
              </li>
            </template>
          </ul>
        </template>

        <template v-if="manuscript.assoc_names && manuscript.assoc_names.length > 0">
          <h3>Names</h3>
          <ul>
            <li v-for="assocName in manuscript.assoc_names" :key="assocName.id">
              <Link :href="route('frontend.agents.show', { agent: assocName.id })">
                {{ assocName.pref_name }}
              </Link>
              <span class="ml-1" v-if="assocName.rel">({{ assocName.rel.label }})</span>
            </li>
          </ul>
        </template>

        <template v-if="allRelatedMss.length > 0">
          <h3>Related Manuscripts</h3>
          <ul>
            <template v-for="relatedMss in allRelatedMss" :key="relatedMss.label">
              <li v-for="ms in relatedMss.mss">
                <a :href="getManuscriptLink(ms).url" :target="getManuscriptLink(ms).isExternal ? '_blank' : '_self'">
                  {{ ms.label }} ({{ relatedMss.type.label }})
                  <span v-if="getManuscriptLink(ms).isExternal" class="text-xs absolute ml-1">↗</span>
                </a>
              </li>
            </template>
          </ul>
        </template>

        <template v-if="manuscriptJson.features && manuscriptJson.features.length > 0">
          <h3>Keywords</h3>
          <ul>
            <li v-for="keyword in manuscriptJson.features" :key="keyword.id">
              <Link :href="`${route('frontend.manuscripts.index')}?filters=${encodeURIComponent(JSON.stringify(['features:' + keyword.label]))}`">
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
  import AssociatedDates from "@/Pages/Frontend/Browse/Components/AssociatedDates.vue";

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

  const shelfmarkVariants = computed(() => {
    return props.manuscript.references
        .filter(reference => reference.alt_shelf)
        .map(reference => `${reference.alt_shelf} (${reference.short_title})`)
        .join(', ');
  });

  const primaryLanguage = computed(() => {
    return props.manuscript.related_overtext_layers
        .flatMap(layer => layer.json.text_unit.map(textUnit => textUnit.label))
        .join(', ');
  });

  const scriptLabels = computed(() => {
    return props.manuscript.related_overtext_layers
        .flatMap(layer => layer.json.writing.flatMap(writing => writing.script.map(script => script.label)))
        .join(', ');
  });

  const hasUndertextObjects = computed(() => {
    const hasLayerUndertext = manuscriptJson.value.layer && manuscriptJson.value.layer.some(layer => layer.type.id === 'undertext');
    const hasPartUndertext = manuscriptJson.value.part && manuscriptJson.value.part.some(part => part.layer && part.layer.some(layer => layer.type.id === 'undertext'));
    return hasLayerUndertext || hasPartUndertext;
  });

  const hasGuestContent = computed(() => {
    const hasLayerGuest = manuscriptJson.value.layer && manuscriptJson.value.layer.some(layer => layer.type.id === 'guest');
    const hasPartGuest = manuscriptJson.value.part && manuscriptJson.value.part.some(part => part.layer && part.layer.some(layer => layer.type.id === 'guest'));

    return hasLayerGuest || hasPartGuest;
  });

  const hasParaGuestContent = computed(() => {
    const hasPartPara = manuscriptJson.value.part && manuscriptJson.value.part.some(part => part.para && part.para.length > 0);
    const hasGlobalPara = manuscriptJson.value.para && manuscriptJson.value.para.length > 0;
    return hasPartPara || hasGlobalPara;
  });

  const allInscribedLayers = computed(() => {
    const rootLayers = manuscriptJson.value.layer || [];
    const partLayers = manuscriptJson.value.part
        ? manuscriptJson.value.part.flatMap(part => part.layer || [])
        : [];

    const combinedLayers = [...rootLayers, ...partLayers];
    return combinedLayers.sort((a, b) => {
      const order = { overtext: 1, undertext: 2, guest: 3 };
      return (order[a.type.id] || 4) - (order[b.type.id] || 4);
    });
  });

  const getPartRelatedMss = () => {
    return manuscriptJson.value.part
        ? manuscriptJson.value.part.flatMap(part => part.related_mss || [])
        : [];
  };

  const partRelatedMss = computed(() => getPartRelatedMss());

  const allRelatedMss = computed(() => {
    const rootRelatedMss = manuscriptJson.value.related_mss || [];
    return [...getPartRelatedMss(), ...rootRelatedMss];
  });

  const hasResources = computed(() => {
    const hasReferences = props.manuscript.references && props.manuscript.references.length > 0;
    const hasBibliographies = props.manuscript.bibliographies && props.manuscript.bibliographies.length > 0;
    const hasRelatedDigitalVersions = props.manuscript.related_digital_versions && props.manuscript.related_digital_versions.length > 0;
    const hasViscodex = manuscriptJson.viscodex && manuscriptJson.viscodex.length > 0;
    return hasReferences || hasBibliographies || hasRelatedDigitalVersions || hasViscodex;
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

  ul li {
    @apply my-2 text-base xl:text-lg
  }

  ul.bulleted {
    @apply list-disc ml-4
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

  .indent {
    @apply md:ml-4 block
  }
</style>