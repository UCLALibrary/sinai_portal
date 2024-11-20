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

        <p class="italic">
          {{ manuscriptJson.summary }}
        </p>

        <p class="mb-8">
          {{ manuscript.assoc_dates_overview.join('; ') }}
        </p>

        <div v-if="manuscriptJson.ark && manuscriptJson.ark !== ''" class="item-container">
          <span class="item-label">Ark</span>
          <p class="item-value">
            {{ manuscriptJson.ark }}
          </p>
        </div>

        <div v-if="manuscript.related_references && manuscript.related_references.length > 0" class="item-container">
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

        <template v-if="manuscriptJson.part && manuscriptJson.part.length > 0">
          <h3>Parts</h3>

          <template v-for="(part) in manuscriptJson.part">
            <div class="mb-8">
              <p class="mb-0">
                <strong>{{ part.label }}</strong><template v-if="part.locus">, {{ part.locus }}</template>
              </p>

              <p class="italic">
                {{ part.summary }}
              </p>

              <template v-if="part.support && part.support.length > 0">

                <div class="item-container">
                  <span class="item-label">Support</span>
                  <p class="item-value">
                    <span class="d-block">{{ part.support.map(support => support.label).join(', ') }}</span>
                    <span class="d-block" v-if="part.note && part.note.filter(note => note.type.id === 'support').length > 0">
                      {{ part.note.filter(note => note.type.id === 'support').map(note => note.value).join(', ') }}
                    </span>
                  </p>
                </div>

              </template>

              <div class="item-container">
                <span class="item-label">Extent</span>
                <p class="d-block">{{ part.extent }} | {{ part.dim }}</p>
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

              <div v-if="part.layer && part.layer.length > 0">
                <div class="item-container">
                  <span class="item-label">Contents</span>
                  <p class="item-value">
                    <template v-for="(layer) in part.layer.filter(layer => layer.type.id === 'overtext')" class="d-block">
                      <span class="d-block">
                        {{ layer.label }}<template v-if="layer.locus">; {{ layer.locus }}</template>
                      </span>

                      <span class="d-block">
                        <template v-for="(date, index) in manuscript.assoc_dates_from_layers" :key="index">
                          <template v-if="date.id === layer.id.split('/').pop()" class="d-block">
                            {{ date.assoc_date_value }}.
                          </template>
                        </template>
                        <template v-for="(place, index) in manuscript.assoc_places_from_layers" :key="index">
                          <template v-if="place.id === layer.id.split('/').pop()" class="d-block">
                            {{ place.assoc_place_value }}.
                          </template>
                        </template>
                         <template v-for="(lang, index) in manuscript.lang_from_parts_layers_text_units" :key="index">
                          <template v-if="lang.layer_id === layer.id.split('/').pop()" class="d-block">
                            {{ lang.lang_label }}.
                          </template>
                        </template>
                      </span>
                    </template>
                  </p>
                </div>

              </div>

            </div>
          </template>
        </template>

        
        <template v-if="hasUndertextObjects">
          <p>
            <strong>Undertext Objects</strong>
          </p>

          <template v-if="manuscriptJson.part && manuscriptJson.part.length > 0">
            <template v-for="(part) in manuscriptJson.part">
              <template v-if="part.layer && part.layer.filter(layer => layer.type.id === 'undertext').length > 0">
                <p v-for="(layer) in part.layer.filter(layer => layer.type.id === 'undertext')">
                  {{ part.label }}, {{ layer.label }}, {{ layer.locus }}
                </p>
              </template>
            </template>
          </template>

          <template v-if="manuscriptJson.layer && manuscriptJson.layer.length > 0">
            <p v-for="(layer) in manuscriptJson.layer.filter(layer => layer.type.id === 'undertext')">
              {{ layer.label }}, {{ layer.locus }}
            </p>
          </template>

        </template>

        <template v-if="hasGuestContent">
          <p>
            <strong>Guest Content</strong>
          </p>

          <template v-for="part in manuscriptJson.part">
            <template v-if="part.layer && part.layer.filter(layer => layer.type.id === 'guest').length > 0">
              <p v-for="(layer) in part.layer.filter(layer => layer.type.id === 'guest')">
                {{ part.label }}, {{ layer.label }}, {{ layer.locus }}
              </p>
            </template>
          </template>

          <p v-for="layer in manuscriptJson.layer.filter(layer => layer.type.id === 'guest')">
            {{ layer.label }}, {{ layer.locus }}
          </p>

        </template>

        <template v-if="hasParaGuestContent">
          <h3>Paracontent</h3>

          <template v-if="manuscript.parts && manuscript.parts.length > 0">
            <template v-for="part in manuscript.parts">
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
                  <p v-if="para.translation && para.translation.length > 0" class="indent">
                    Translation: {{ para.translation.join('; ') }}
                  </p>

                  <p v-if="(para.assoc_name && para.assoc_name.length > 0) || (para.assoc_place && para.assoc_place.length > 0) || (para.assoc_date && para.assoc_date.length > 0)" class="indent">
                    <strong>Associated Names, Places, Dates</strong>
                  </p>

                  <div v-if="para.assoc_name && para.assoc_name.length > 0">
                    <ul class="indent">
                      <li v-for="name in para.assoc_name">
                        {{ name.role.label }}: {{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="name.note && name.note.length > 0" class="indent">
                          {{ name.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_place && para.assoc_place.length > 0">
                    <ul class="indent">
                      <li v-for="place in para.assoc_place">
                        {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
                        <span v-if="place.note && place.note.length > 0" class="indent">
                          {{ place.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_date && para.assoc_date.length > 0">
                    <ul class="indent">
                      <li v-for="date in para.assoc_date">
                        {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
                        <span v-if="date.note && date.note.length > 0" class="indent">
                          {{ date.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <template v-if="para.note && para.note.length > 0" class="indent">
                    <p class="indent">
                      <strong>Notes</strong>
                    </p>

                    <p class="indent">
                      {{ para.note.join('; ') }}
                    </p>
                  </template>
                </div>
              </template>
            </template>
          </template>

          <template v-if="manuscript.para && manuscript.para.length > 0">
            <div v-for="para in manuscript.para" class="mb-8">
              <p>
                <strong>{{ para.locus }}, {{ para.label }}, {{ para.type.label }}</strong>
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

              <p v-if="(para.assoc_name && para.assoc_name.length > 0) || (para.assoc_place && para.assoc_place.length > 0) || (para.assoc_date && para.assoc_date.length > 0)" class="indent">
                <strong>Associated Names, Places, Dates</strong>
              </p>

              <template v-if="para.assoc_name && para.assoc_name.length > 0">
                <ul class="indent">
                  <li v-for="name in para.assoc_name">
                    {{ name.role.label }}: {{ [name.as_written, name.pref_name].filter(Boolean).join(' | ') }}
                    <span v-if="name.note && name.note.length > 0" class="indent">
                      {{ name.note.join('; ') }}
                    </span>
                  </li>
                </ul>
              </template>

              <div v-if="para.assoc_place && para.assoc_place.length > 0" class="mt-2">
                <ul class="indent">
                  <li v-for="(place, placeIndex) in para.assoc_place" :key="placeIndex">
                    {{ place.event.label }}: {{ [place.as_written, place.pref_name].filter(Boolean).join(' | ') }}
                    <span v-if="place.note && place.note.length > 0" class="indent">
                      {{ place.note.join('; ') }}
                    </span>
                  </li>
                </ul>
              </div>

              <div v-if="para.assoc_date && para.assoc_date.length > 0" class="mt-2">
                <ul class="indent">
                  <li v-for="(date, dateIndex) in para.assoc_date" :key="dateIndex">
                    {{ date.type.label }}: {{ [date.as_written, date.value].filter(Boolean).join(' | ') }}
                    <span v-if="date.note && date.note.length > 0" class="indent">
                      {{ date.note.join('; ') }}
                    </span>
                  </li>
                </ul>
              </div>

              <template v-if="para.note && para.note.length > 0">
                <p class="indent">
                  <strong>
                    Notes
                  </strong>
                </p>
                <p class="indent">
                  {{ para.note.join('; ') }}
                </p>
              </template>
            </div>
          </template>
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
              <ul class="indent">
                <li v-for="ms in relatedMss.mss">
                  <a :href="getRelatedMsLink(ms).url" :target="getRelatedMsLink(ms).isExternal ? '_blank' : '_self'">
                    {{ ms.label }}
                  </a>
                </li>
              </ul>
              <p v-if="relatedMss.note && relatedMss.note.length > 0" class="indent">
                {{ relatedMss.note.join(", ") }}
              </p>
            </div>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'binding').length > 0">
          <h3>Notes</h3>
          <p>
            <strong>Binding</strong>
          </p>
          <ul>
            <li v-for="(bindingNote) in manuscriptJson.note.filter(note => note.type.id === 'binding')">
              {{ bindingNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'provenance').length > 0">
          <p>
            <strong>Provenance</strong>
          </p>
          <ul>
            <li v-for="(provenanceNote) in manuscriptJson.note.filter(note => note.type.id === 'provenance')">
              {{ provenanceNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'condition').length > 0">
          <p>
            <strong>Condition</strong>
          </p>
          <ul>
            <li v-for="(conditionNote) in manuscriptJson.note.filter(note => note.type.id === 'condition')">
              {{ conditionNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'general').length > 0">
          <p>
            <strong>Other notes:</strong>
          </p>
          <ul>
            <li v-for="(generalNote) in manuscriptJson.note.filter(note => note.type.id === 'general')">
              {{ generalNote .value }}
            </li>
          </ul>
        </template>

        <template v-if="(manuscript.related_agents && manuscript.related_agents.length > 0) || (manuscript.related_places && manuscript.related_places.length > 0) || (manuscriptJson.assoc_date && manuscriptJson.assoc_date.length > 0)">
          <p class="mt-8">
            <strong>Associated Names, Places, Dates</strong>
          </p>

          <div v-for="relatedAgent in manuscript.related_agents" class="mb-8">
            <p>
              <template v-if="relatedAgent.role.label">{{ relatedAgent.role.label }}: </template>{{ [relatedAgent.as_written, relatedAgent.pref_name].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="relatedAgent.note && relatedAgent.note.length > 0" class="indent">
              {{ relatedAgent.note.join(", ") }}
            </p>
          </div>

          <div v-for="relatedPlace in manuscript.related_places" class="mb-8">
            <p>
              {{ relatedPlace.event.label }}: {{ [relatedPlace.as_written, relatedPlace.pref_name].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="relatedPlace.note && relatedPlace.note.length > 0" class="indent">
              {{ relatedPlace.note.join(", ") }}
            </p>
          </div>

          <div v-for="relatedDate in manuscriptJson.assoc_date" class="mb-8">
            <p>
              {{ relatedDate.type.label }}: {{ [relatedDate.as_written, relatedDate.value].filter(Boolean).join(' | ') }}
            </p>
            <p v-if="relatedDate.note && relatedDate.note.length > 0" class="indent">
              {{ relatedDate.note.join(", ") }}
            </p>
          </div>
        </template>

        <h3>Resources</h3>
        <div v-if="manuscript.related_references && manuscript.related_references.length > 0" class="item-container">
          <span class="item-label">References</span>
          <div class="item-value">
            <template v-for="reference in manuscript.related_references">
              <p>
                {{ reference.short_title }}, {{ reference.range }}<span v-if="reference.alt_shelf">. Reference mark: {{ reference.alt_shelf }}</span>
              </p>
              <p v-for="note in reference.note">
                {{ note }}
              </p>
            </template>
          </div>
        </div>

        <div v-if="manuscript.related_bibliographies && manuscript.related_bibliographies.length > 0" class="item-container">
          <span class="item-label">Bibliography</span>
          <div class="item-value">
            <template v-for="bibliography in manuscript.related_bibliographies">
              <p>
                <a v-if="bibliography.url" :href="bibliography.url" target="_blank">
                  {{ bibliography.formatted_citation }}
                </a>
                <span v-else>
                {{ bibliography.formatted_citation }}
              </span>
                <span v-if="bibliography.range">. {{ bibliography.range }}</span>
              </p>
            </template>
          </div>
        </div>

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
            <li v-for="textUnit in manuscript.related_text_units">
              <Link :href="route('frontend.textunits.show', { textunit: textUnit.id })">
                {{ textUnit.label }}
              </Link>
            </li>
          </ul>
        </template>

        <template v-if="manuscript.related_agents && manuscript.related_agents.length > 0">
          <h3>Names</h3>
          <ul>
            <li v-for="relatedAgent in manuscript.related_agents" :key="relatedAgent.id">
              <Link :href="route('frontend.agents.show', { agent: relatedAgent.id })">
                {{ relatedAgent.pref_name }}
              </Link>
              <span class="ml-1" v-if="relatedAgent.rel">({{ relatedAgent.rel.label }})</span>
            </li>
          </ul>
        </template>

        <template v-if="partRelatedMss.length > 0">
          <h3>Related Manuscripts</h3>
          <ul>
            <template v-for="relatedMss in partRelatedMss" :key="relatedMss.label">
              <li v-for="ms in relatedMss.mss">
                <a :href="getRelatedMsLink(ms).url" :target="getRelatedMsLink(ms).isExternal ? '_blank' : '_self'">
                  {{ ms.label }} ({{ relatedMss.type.label }})
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
    return props.manuscript.related_references
        .map(reference => reference.alt_shelf
            ? `${reference.short_title} (${reference.alt_shelf})`
            : reference.short_title)
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

  const getRelatedMsLink = (ms) => {
    let url;
    let isExternal = false;

    if (ms.url) {
      url = ms.url;
      isExternal = true;
    } else if (ms.id) {
      const arkId = ms.id.split('/').pop();
      url = `/manuscripts/${arkId}`;
    } else {
      url = '#';
    }

    return { url, isExternal };
  };

  const hasResources = computed(() => {
    const hasReferences = props.manuscript.related_references && props.manuscript.related_references.length > 0;
    const hasBibliographies = props.manuscript.related_bibliographies && props.manuscript.related_bibliographies.length > 0;
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
    @apply text-black border-black border-b border-dotted hover:border-solid
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