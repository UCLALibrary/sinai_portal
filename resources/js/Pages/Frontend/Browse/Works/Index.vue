<template>
  <FrontendLayout :title="title">
    <AisInstantSearch
      v-if="initialUiState"
      :index-name="indexName"
      :search-client="searchClient"
      :initial-ui-state="initialUiState"
      :future="{ preserveSharedStateOnUnmount: true }">
      <div class="facet-sidebar">
        <div class="flex justify-between">
          <span class="font-dosis text-lg font-medium uppercase">
            Search Works
          </span>

          <AisClearRefinements>
            <template v-slot="{ refine, createURL }">
              <a
                v-show="isAnyFilterApplied"
                :href="createURL()"
                class="w-auto"
                @click.prevent="onClearRefinements(refine)">
                <span class="clear-filter">
                  Clear Filters
                  <button type="button">
                    <span class="sr-only">Clear Filters</span>
                    <svg viewBox="0 0 14 14" class="h-full w-full stroke-black">
                      <path d="M4 4l6 6m0-6l-6 6" />
                    </svg>
                    <span class="-inset-1" />
                  </button>
                </span>
              </a>
            </template>
          </AisClearRefinements>
        </div>

        <AisSearchBox
          placeholder="Search term"
          submit-title="Search"
          :autofocus="true"
          :show-loading-indicator="true"
          @keyup="onKeyup"
          @reset="onReset">
          <template v-slot:submit-icon>
            <img src="/img/search.svg" alt="Search" title="Search" class="w-6 h-6">
          </template>
        </AisSearchBox>

        <img src="/img/algolia-logo-black.svg" alt="Algolia" class="w-14 opacity-60 self-end -mt-2">

        <div class="accordion-items">
          <AccordionCard title="Original Language" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRefinementList
                  :limit="maxFacetValuesToShow"
                  attribute="orig_lang_label"
                  @change="onFilter('type', $event.target.value)"
                  class="max-h-48 overflow-y-scroll my-4">
                  <template v-slot="{ items }">
                    <div v-if="!items.length">
                      No results found
                    </div>
                  </template>
                </AisRefinementList>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>

          <AccordionCard title="Genre" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRefinementList
                  :limit="maxFacetValuesToShow"
                  attribute="genre"
                  @change="onFilter('genre', $event.target.value)"
                  class="max-h-48 overflow-y-scroll my-4">
                  <template v-slot="{ items }">
                    <div v-if="!items.length">
                      No results found
                    </div>
                  </template>
                </AisRefinementList>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>

           <AccordionCard title="Creator" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRefinementList
                  :limit="maxFacetValuesToShow"
                  attribute="creator"
                  @change="onFilter('creator', $event.target.value)"
                  class="max-h-48 overflow-y-scroll my-4">
                  <template v-slot="{ items }">
                    <div v-if="!items.length">
                      No results found
                    </div>
                  </template>
                </AisRefinementList>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>

          <AccordionCard v-if="showDateRangeFilter" title="Dates" :toggleable="false" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRangeInput
                  attribute="date_min"
                  class="px-3 pt-10">
                  <template v-slot="{ currentRefinement, range, refine }">
                    <VRangeSlider
                      :min="minMaxRangeValues.date[0]"
                      :max="minMaxRangeValues.date[1]"
                      v-model="rangeFilters.date"
                      @end="onRangeFilter('date', $event[0], $event[1])"
                      step="1"
                      thumb-label="always">
                    </VRangeSlider>
                  </template>
                </AisRangeInput>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>
        </div>
      </div>

      <AisConfigure v-if="dateRangeFilterQuery" :filters="dateRangeFilterQuery" />

      <div class="main-container">
        <SearchResultsHeader
          record-type="works"
        />

        <div class="hidden lg:grid lg:grid-cols-5 gap-x-1 p-2 font-bold border-b">
          <h3>Title</h3>
          <h3>Language</h3>
          <h3>Genre</h3>
          <h3>Date</h3>
          <h3>ARK</h3>
        </div>
        <InfiniteHits>
          <template v-slot:loading-indicator>
            <AisStateResults>
              <template v-slot="{ status }">
                <LoadingIndicator
                  v-show="status === 'loading' || status === 'stalled'"
                />
              </template>
            </AisStateResults>
          </template>
  
          <template v-slot:item="{ item }">
            <WorkResult
              :result="item"
            />
          </template>
        </InfiniteHits>
      </div>
    </AisInstantSearch>
  </FrontendLayout>
</template>

<script setup>
  import { ref, computed, onBeforeMount } from 'vue'
  import FrontendLayout from '@/Layouts/FrontendLayout.vue'
  import WorkResult from '@/Shared/Search/WorkResult.vue'
  import AccordionCard from '@/Shared/Accordion/AccordionCard.vue'
  import LoadingIndicator from '@/Shared/LoadingIndicator/LoadingIndicator.vue'
  import SearchResultsHeader from '@/Shared/Search/SearchResultsHeader.vue'
  import {
    AisInstantSearch,
    AisConfigure,
    AisStateResults,
    AisSearchBox,
    AisDynamicWidgets,
    AisRefinementList,
    AisRangeInput,
    AisClearRefinements
  } from 'vue-instantsearch/vue3/es'
  import InfiniteHits from '@/Shared/Search/InfiniteHits.vue'

  const props = defineProps({
    title: { type: String, required: true },
    appId: { type: String, required: true },
    apiKey: { type: String, required: true },
    indexName: { type: String, required: true },
    searchQuery: { type: String, required: true },
  })

  import algoliasearch from 'algoliasearch/lite'
  const searchClient = algoliasearch(props.appId, props.apiKey)

  import useSearchBox from '@/composables/search/useSearchBox'
  const { query, onKeyup, onReset } = useSearchBox(props.searchQuery)

  import useFacetFilters from '@/composables/search/useFacetFilters'
  const { filters, onFilter, onClearFilters } = useFacetFilters()

  const maxFacetValuesToShow = 100

  const initialUiState = ref(null)

  import useDateRangeFilter from '@/composables/search/useDateRangeFilter'
  const {
    showDateRangeFilter,
    minMaxRangeValues,
    rangeFilters,
    onRangeFilter,
    onClearRangeFilters,
    dateRangeFilterQuery,
    isDateRangeFilterApplied,
  } = useDateRangeFilter(searchClient.initIndex(props.indexName))

  onBeforeMount(() => {
    // initialize the state of the user interface
    initialUiState.value = {
      [props.indexName]: {
        query: query.value,
        refinementList: filters.value,
      }
    }
  })

  const isAnyFilterApplied = computed(() => {
    const anyFacetFiltersApplied = Object.keys(filters.value).some(
      key => filters.value[key].length > 0
    )
    return anyFacetFiltersApplied || isDateRangeFilterApplied.value
  })

  const onClearRefinements = (refine) => {
    onClearFilters(refine)
    onClearRangeFilters()
  }
</script>

<style lang="postcss">
  /*
   * Search container
   */

  .facet-sidebar {
    @apply flex flex-col gap-y-4 lg:sticky lg:h-screen top-0 p-4 lg:w-1/3 2xl:w-1/4 bg-sinai-gray
  }

  .main-container {
    @apply w-full
  }

  .filter-bar {
    @apply lg:sticky top-0 z-10 pt-5 pb-1 lg:pr-10 flex justify-between items-center bg-gradient-to-t from-transparent to-gray-300
  }

  .ais-InstantSearch {
    @apply flex flex-col lg:flex-row gap-x-4 mb-4 w-full
  }

  /*
   * Search form
   */

  /* clears the ‘X’ from Chrome */
  input[type="search"]::-webkit-search-decoration,
  input[type="search"]::-webkit-search-cancel-button,
  input[type="search"]::-webkit-search-results-button,
  input[type="search"]::-webkit-search-results-decoration {
    display: none;
  }

  /* the root element of the widget */
  .ais-SearchBox {}

  /* the form element */
  .ais-SearchBox-form {
    @apply flex relative
  }

  /* the input element */
  .ais-SearchBox-input {
    @apply block w-full pl-2 pr-8 max-sm:text-[16px] text-base border-b-2 border-black max-w-none rounded focus:ring-gray-900
  }

  /* the submit button element */
  .ais-SearchBox-submit {
    @apply absolute inset-y-0 right-0 pr-1 flex items-center cursor-pointer  
  }

  /* the submit button icon */
  .ais-SearchBox-submitIcon {}

  /* the reset button element */
  .ais-SearchBox-reset {
    @apply absolute inset-y-2 right-1 z-10 bg-white w-6 h-6
  }

  /* the reset button icon */
  .ais-SearchBox-resetIcon {
    @apply !w-4 !h-4
  }

  /* the loading indicator element */
  .ais-SearchBox-loadingIndicator {}

  /* the loading indicator icon */
  .ais-SearchBox-loadingIcon {}


  /*
   * Facets
   */

  .facet-sidebar .accordion-items {
    @apply flex flex-col gap-y-2 overflow-y-scroll pr-4 -mr-4
  }

  .facet-sidebar .accordion-item {
    @apply border-t border-black border-dotted pt-2
  }

  .facet-sidebar .accordion-item .icon {
    @apply text-sinai-red
  }

  .facet-sidebar .accordion-item .title {
    @apply uppercase
  }

  .ais-RefinementList {}

  .clear-filter {
    @apply inline-flex items-center gap-x-1 rounded-full bg-white px-2 py-1 text-sm font-sans text-black shadow-lg hover:bg-sinai-light-blue
  }

  .clear-filter button {
    @apply relative -mr-1 h-5 w-5
  }

  .ais-ClearRefinements-button {
    @apply block
  }

  .ais-ClearRefinements-button--disabled {
    @apply hidden
  }

  .ais-RefinementList-showMore {
    @apply border-b border-dotted hover:border-solid font-sans max-sm:text-[14px] text-sm lowercase
  }

  .ais-RefinementList-showMore--disabled {
    @apply hidden
  }

  .ais-RefinementList-item {
    @apply flex cursor-pointer
  }

  .ais-RefinementList-label {
    @apply flex items-center gap-x-2 mb-1.5 text-sm lg:text-base cursor-pointer focus:!ring-0
  }

  .ais-RefinementList-checkbox {
    @apply rounded cursor-pointer w-4 h-4 shadow-none ml-1 focus:ring-0 focus:ring-offset-0
  }

  .ais-RefinementList-checkbox:checked {
    @apply bg-black text-black
  }

  .ais-RefinementList-labelText {
    @apply flex-1 max-sm:text-[14px] text-sm lg:text-base
  }

  .ais-RefinementList-count {
    @apply bg-gray-100 px-1 text-center min-w-6 rounded-full text-xs text-black
  }


  /*
   * Hits
   */

  .ais-Hits {}

  .ais-Hits-list {
    @apply divide-y divide-black divide-dotted w-full
  }

  .ais-Hits-item {
    @apply py-4 sm:px-2 hover:bg-white
  }


  /*
   * Pagination
   */

  /* the root of the widget */
  .ais-Pagination {
    @apply border-t-2 border-black mt-12 pt-4;
  }

  /* the root of the widget without refinement */
  .ais-Pagination--noRefinement {
    @apply hidden;
  }

  /* the list of the page items */
  .ais-Pagination-list {
    @apply flex justify-start;
  }

  /* the page item of the list */
  .ais-Pagination-item {
    @apply px-1 font-sans;
  }

  /* the "first" item of the list */
  .ais-Pagination-item--firstPage {}

  /* the "last" item of the list */
  .ais-Pagination-item--lastPage {}

  /* the "previous" item of the list */
  .ais-Pagination-item--previousPage {}

  /* the "next" item of the list */
  .ais-Pagination-item--nextPage {}

  /* the "page" item of the list */
  .ais-Pagination-item--page {}

  /* the selected item of the list */
  .ais-Pagination-item--selected {
    @apply font-bold;
  }

  /* the disabled item of the list */
  .ais-Pagination-item--disabled {}

  /* the clickable element of an item */
  .ais-Pagination-link {
    @apply text-black hover:font-bold cursor-pointer p-4 !border-0;
  }
</style>
