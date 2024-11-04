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

        <p class="mb-8 italic">
          {{ manuscriptJson.summary }}
        </p>

        <div v-if="manuscriptJson.ark && manuscriptJson.ark !== ''" class="item-container">
          <span class="item-label">Ark</span>
          <p class="item-value">
            {{ manuscriptJson.ark }}
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

        <template v-if="manuscriptJson.part && manuscriptJson.part.length > 0">
          <h3>Parts</h3>

          <template v-for="(part) in manuscriptJson.part">
            <div class="mb-12">
              <p class="mb-0">
                <strong>{{ part.label }}</strong>, {{ part.locus }}
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
                    <span v-for="(layer) in part.layer.filter(layer => layer.type.id === 'overtext')" class="d-block">
                    {{ layer.label }}, {{ layer.locus }}
                  </span>
                  </p>
                </div>

              </div>

              <template v-if="part.related_mss && part.related_mss.length > 0">
                <div v-for="(relatedMss) in part.related_mss" class="item-container">
                  <span class="item-label">Related Manuscripts</span>
                  <div class="item-value">
                    <p>{{ relatedMss.label }} ({{ relatedMss.type.label }})</p>
                    <ul>
                      <li v-for="(mss) in relatedMss.mss">
                        <a :href="mss.url" target="_blank">{{ mss.label }}</a>
                      </li>
                    </ul>
                    <template v-if="relatedMss.note && relatedMss.note.length > 0">
                      {{ relatedMss.note.join(", ") }}
                    </template>
                  </div>
                </div>
              </template>

            </div>
          </template>
        </template>

        <template v-if="manuscriptJson.layer && manuscriptJson.layer.length > 0">
          <h3>MS Section</h3>

          <template v-if="hasUndertextObjects">
            <h4>Undertext Objects</h4>

            <template v-for="(part) in manuscriptJson.part">
              <template v-if="part.layer && part.layer.filter(layer => layer.type.id === 'uto').length > 0">
                <p v-for="(layer) in part.layer.filter(layer => layer.type.id === 'uto')">
                  {{ part.label }}, {{ layer.label }}, {{ layer.locus }}
                </p>
              </template>
            </template>

            <p v-for="(layer) in manuscriptJson.layer.filter(layer => layer.type.id === 'uto')">
              {{ layer.label }}, {{ layer.locus }}
            </p>

          </template>

          <template v-if="hasGuestContent">
            <h4>Guest Content</h4>

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
        </template>

        <template v-if="hasParaGuestContent">
          <h3>Para/Guest Content</h3>

          <template v-if="manuscriptJson.part && manuscriptJson.part.length > 0">
            <template v-for="(part, index) in manuscriptJson.part" :key="index">
              <div v-if="part.para && part.para.length > 0" class="mb-12">
                <p>
                  <strong>{{ part.label }}</strong>
                </p>

                <template v-for="(para, paraIndex) in part.para" :key="paraIndex">
                  <p>
                    <strong>{{ para.locus }}, {{ para.label }} ({{ para.type.label }})</strong>
                  </p>
                  <p v-if="para.lang && para.lang.length > 0">
                    {{ para.lang.map(lang => lang.label).join(', ') }}
                  </p>
                  <p v-if="para.script && para.script.length > 0">
                    {{ para.script.map(script => script.label).join(', ') }}
                  </p>
                  <p v-if="para.as_written">
                    {{ para.as_written }}
                  </p>
                  <p v-if="para.translation && para.translation.length > 0">
                    {{ para.translation.join('; ') }}
                  </p>

                  <div v-if="para.assoc_name && para.assoc_name.length > 0">
                    <strong>Associated Name(s):</strong>
                    <ul>
                      <li v-for="(name, nameIndex) in para.assoc_name" :key="nameIndex">
                        {{ name.role.label }}: [{{ name.as_written }}]
                        <span v-if="name.note && name.note.length > 0">
                          {{ name.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_place && para.assoc_place.length > 0">
                    <strong>Associated Place(s):</strong>
                    <ul>
                      <li v-for="(place, placeIndex) in para.assoc_place" :key="placeIndex">
                        {{ place.event.label }}: [{{ place.as_written }}]
                        <span v-if="place.note && place.note.length > 0">
                          {{ place.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <div v-if="para.assoc_date && para.assoc_date.length > 0" class="mb-2">
                    <strong>Associated Date(s):</strong>
                    <ul>
                      <li v-for="(date, dateIndex) in para.assoc_date" :key="dateIndex">
                        {{ date.type.label }}: [{{ date.as_written }}] - Value: {{ date.value }}
                        <span v-if="date.note && date.note.length > 0">
                          {{ date.note.join('; ') }}
                        </span>
                      </li>
                    </ul>
                  </div>

                  <p v-if="para.note && para.note.length > 0" class="mt-2">
                    {{ para.note.join('; ') }}
                  </p>
                </template>
              </div>
            </template>
          </template>

          <template v-if="manuscriptJson.para && manuscriptJson.para.length > 0">
            <div v-for="(para, paraIndex) in manuscriptJson.para" :key="paraIndex" class="mb-12">
              <p>
                <strong>{{ para.locus }}, {{ para.label }}, {{ para.type.label }}</strong>
              </p>
              <p v-if="para.lang && para.lang.length > 0">
                {{ para.lang.map(lang => lang.label).join(', ') }}
              </p>
              <p v-if="para.as_written">
                {{ para.as_written }}
              </p>
              <p v-if="para.translation && para.translation.length > 0">
                {{ para.translation.join('; ') }}
              </p>

              <template v-if="para.assoc_name && para.assoc_name.length > 0">
                <strong>Associated Name(s):</strong>
                <ul>
                  <li v-for="(name, nameIndex) in para.assoc_name" :key="nameIndex">
                    {{ name.role.label }}: [{{ name.as_written }}]
                    <span v-if="name.note && name.note.length > 0">
              {{ name.note.join('; ') }}
            </span>
                  </li>
                </ul>
              </template>

              <div v-if="para.assoc_place && para.assoc_place.length > 0" class="mt-2">
                <strong>Associated Place(s):</strong>
                <ul>
                  <li v-for="(place, placeIndex) in para.assoc_place" :key="placeIndex">
                    {{ place.event.label }}: [{{ place.as_written }}]
                    <span v-if="place.note && place.note.length > 0">
              {{ place.note.join('; ') }}
            </span>
                  </li>
                </ul>
              </div>

              <div v-if="para.assoc_date && para.assoc_date.length > 0" class="mt-2">
                <strong>Associated Date(s):</strong>
                <ul>
                  <li v-for="(date, dateIndex) in para.assoc_date" :key="dateIndex">
                    {{ date.type.label }}: [{{ date.as_written }}] - Value: {{ date.value }}
                    <span v-if="date.note && date.note.length > 0">
              {{ date.note.join('; ') }}
            </span>
                  </li>
                </ul>
              </div>

              <p v-if="para.note && para.note.length > 0" class="mt-2">
                {{ para.note.join('; ') }}
              </p>
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

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'ornament').length > 0">
          <h3>Ornamentation</h3>
          <ul>
            <li v-for="(ornamentNote) in manuscriptJson.note.filter(note => note.type.id === 'ornament')">
              {{ ornamentNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.related_mss">
          <h3>Related Manuscripts</h3>
          <template v-for="relatedMss in manuscriptJson.related_mss">
            <p>{{ relatedMss.label }} ({{ relatedMss.type.label }})</p>
            <p v-for="relatedMssNote in relatedMss.note">
              {{ relatedMssNote }}
            </p>
          </template>
        </template>

        <template v-for="partItem in manuscriptJson.part.filter(part => part.related_mss && part.related_mss.length > 0)">
          <template v-for="relatedMss in partItem.related_mss.filter(mss => mss.mss && mss.mss.length > 0)">
            <template v-for="mssItem in relatedMss.mss">
              <p>
                <a v-if="mssItem.url" :href="mssItem.url" target="_blank">{{ mssItem.label }}</a>
              </p>
            </template>
            <template v-for="relatedMssNote in relatedMss.note">
              <p>
                {{ relatedMssNote }}
              </p>
            </template>
          </template>
        </template>

        <h3>Notes</h3>
        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'binding').length > 0">
          <p>
            <strong>Binding:</strong>
          </p>
          <ul>
            <li v-for="(bindingNote) in manuscriptJson.note.filter(note => note.type.id === 'binding')">
              {{ bindingNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'provenance').length > 0">
          <p>
            <strong>Provenance:</strong>
          </p>
          <ul>
            <li v-for="(provenanceNote) in manuscriptJson.note.filter(note => note.type.id === 'provenance')">
              {{ provenanceNote.value }}
            </li>
          </ul>
        </template>

        <template v-if="manuscriptJson.note && manuscriptJson.note.filter(note => note.type.id === 'condition').length > 0">
          <p>
            <strong>Condition:</strong>
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

        <h3>Resources</h3>

        <div v-if="manuscriptJson.viscodex" class="item-container">
          <span class="item-label">Viscodex</span>
          <div class="item-value">
            <p v-for="viscodex in manuscriptJson.viscodex.filter(viscodex => viscodex.type.id === 'manuscript')">
              <a href="{{ viscodex.url }}">{{ viscodex.label }} ({{ viscodex.type.label }})</a>
            </p>
            <p v-for="viscodex in manuscriptJson.viscodex.filter(viscodex => viscodex.type.id !== 'manuscript')">
              <a href="{{ viscodex.url }}">{{ viscodex.label }} ({{ viscodex.type.label }})</a>
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

        <template v-if="manuscriptJson.layer && manuscriptJson.layer.length > 0">
          <h3>Inscribed Layers</h3>
          <ul>
            <li v-for="(layer) in manuscriptJson.layer">
              {{ layer.label }} ({{ layer.type.label }})
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

        <template v-if="manuscriptJson.features && manuscriptJson.features.length > 0">
          <h3>Keywords</h3>
          <ul>
            <li v-for="keyword in manuscriptJson.features" :key="keyword.id">
              {{ keyword.label }}
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

  const hasUndertextObjects = computed(() => {
    const hasLayerUndertext = manuscriptJson.value.layer && manuscriptJson.value.layer.some(layer => layer.type.id === 'uto');
    const hasPartUndertext = manuscriptJson.value.part && manuscriptJson.value.part.some(part => part.layer && part.layer.some(layer => layer.type.id === 'uto'));
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
    @apply my-2 list-disc ml-4 text-base xl:text-lg
  }

  .sidebar ul li {
    @apply list-none ml-0
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