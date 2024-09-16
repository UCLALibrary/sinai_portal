<template>
  <FrontendLayout :title="title">
    <h2 class="font-dosis flex mx-auto text-3xl border-b pb-2">
      Agents
    </h2>

    <AisInstantSearch
      v-if="initialUiState"
      :index-name="indexName"
      :search-client="searchClient"
      :initial-ui-state="initialUiState"
      :future="{ preserveSharedStateOnUnmount: true }">

      <div class="facet-sidebar">
        <AisSearchBox
          placeholder="Search term"
          submit-title="Search"
          :autofocus="true"
          :show-loading-indicator="true"
          @keyup="onKeyup"
          @reset="onReset">
          <template v-slot:submit-icon>
            <img src="/img/search.svg" alt="Search" title="Search" class="w-7 h-7">
          </template>
        </AisSearchBox>

        <img src="/img/algolia-logo-black.svg" alt="Algolia" class="w-16 opacity-60 self-end -mt-2">
        
        <AisClearRefinements>
          <template v-slot="{ canRefine, refine, createURL }">
            <a
              v-if="canRefine"
              :href="createURL()"
              @click.prevent="onClearFilters(refine)">
              <span class="clear-filter">
                Clear Filters
                <button type="button">
                  <span class="sr-only">Clear Filters</span>
                  <svg viewBox="0 0 14 14" class="h-full w-full stroke-white">
                    <path d="M4 4l6 6m0-6l-6 6" />
                  </svg>
                  <span class="-inset-1" />
                </button>
              </span>
            </a>
          </template>
        </AisClearRefinements>

        <div class="accordion-items">
          <AccordionCard title="Type" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRefinementList
                  :show-more-limit="maxFacetValuesToShow"
                  attribute="type"
                  :show-more="true"
                  @change="onFilter('type', $event.target.value)">
                  <template v-slot="{ items }">
                    <div v-if="!items.length">
                      No results found
                    </div>
                  </template>

                  <template v-slot:showMoreLabel="{ isShowingMore }">
                    {{ !isShowingMore ? 'Show more' : 'Show less' }}
                  </template>
                </AisRefinementList>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>

          <AccordionCard title="Gender" class="accordion-item">
            <template v-slot:content>
              <AisDynamicWidgets :max-values-per-facet="maxFacetValuesToShow">
                <AisRefinementList
                  :show-more-limit="maxFacetValuesToShow"
                  attribute="gender"
                  :show-more="true"
                  @change="onFilter('gender', $event.target.value)">
                  <template v-slot="{ items }">
                    <div v-if="!items.length">
                      No results found
                    </div>
                  </template>

                  <template v-slot:showMoreLabel="{ isShowingMore }">
                    {{ !isShowingMore ? 'Show more' : 'Show less' }}
                  </template>
                </AisRefinementList>
              </AisDynamicWidgets>
            </template>
          </AccordionCard>
        </div>
      </div>

      <div class="main-container">
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
            <AgentResult
              :result="item"
            />
          </template>
        </InfiniteHits>
      </div>
    </AisInstantSearch>
  </FrontendLayout>
</template>

<script setup>
  import { ref, onBeforeMount } from 'vue'
  import FrontendLayout from '@/Layouts/FrontendLayout.vue'
  import AgentResult from '@/Shared/Search/AgentResult.vue'
  import AccordionCard from '@/Shared/Accordion/AccordionCard.vue'
  import LoadingIndicator from '@/Shared/LoadingIndicator/LoadingIndicator.vue'
  import { AisInstantSearch,
    AisStateResults,
    AisSearchBox,
    AisDynamicWidgets,
    AisRefinementList,
    AisClearRefinements
  } from 'vue-instantsearch/vue3/es'
  import InfiniteHits from '@/Shared/Search/InfiniteHits.vue'

  const props = defineProps({
    title: { type: String, required: true },
    appId: { type: String, required: true },
    apiKey: { type: String, required: true },
    indexName: { type: String, required: true },
  })

  import algoliasearch from 'algoliasearch/lite'
  const searchClient = algoliasearch(props.appId, props.apiKey)

  import useSearchBox from '@/composables/search/useSearchBox'
  const { query, onKeyup, onReset } = useSearchBox(props.searchQuery)

  import useFacetFilters from '@/composables/search/useFacetFilters'
  const { filters, onFilter, onClearFilters } = useFacetFilters()
  
  const maxFacetValuesToShow = 200

  const initialUiState = ref(null)

  onBeforeMount(async () => {
    // initialize the state of the user interface
    initialUiState.value = {
      [props.indexName]: {
        query: query.value,
        refinementList: filters.value,
      }
    }
  })
</script>

<style lang="postcss">
  /*
   * Search container
   */

  .facet-sidebar {
    @apply flex flex-col gap-y-4 lg:sticky lg:h-screen top-0 py-5 lg:pl-4 pr-4 lg:w-1/3 2xl:w-1/4 bg-gray-200 opacity-90
  }
  .main-container {
    @apply w-full sm:pl-4 sm:pr-5 py-8 mb-24 lg:p-0
  }
  .filter-bar {
    @apply lg:sticky top-0 z-10 pt-5 pb-1 lg:pr-10 flex justify-between items-center bg-gradient-to-t from-transparent to-gray-300
  }
  .ais-InstantSearch {
    @apply flex flex-col lg:flex-row gap-x-4 my-4 w-full
  }

  /*
   * Search form
   */

  /* clears the ‘X’ from Chrome */
  input[type="search"]::-webkit-search-decoration,
  input[type="search"]::-webkit-search-cancel-button,
  input[type="search"]::-webkit-search-results-button,
  input[type="search"]::-webkit-search-results-decoration { display: none; }

  /* the root element of the widget */
  .ais-SearchBox {}

  /* the form element */
  .ais-SearchBox-form {
    @apply flex relative
  }

  /* the input element */
  .ais-SearchBox-input {
    @apply block w-full pl-2 pr-8 max-sm:text-[16px] text-base border-b-2 border-black max-w-none rounded focus:ring-sinai-red
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
    @apply flex flex-col gap-y-2 overflow-y-scroll pr-4 -mr-4 pb-10
  }
  .facet-sidebar .accordion-item {
    @apply border-b border-black pb-2
  }

  .ais-RefinementList {}

  .clear-filter {
    @apply inline-flex items-center gap-x-1 rounded-full border border-black hover:bg-white px-2 py-1 text-sm font-sans text-black
  }
  .clear-filter button {
    @apply relative -mr-1 h-5 w-5 rounded-full bg-sinai-red
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
    @apply flex items-center gap-x-2 text-sm lg:text-base cursor-pointer
  }

  .ais-RefinementList-checkbox {
    @apply rounded cursor-pointer w-4 h-4 shadow-none ml-1
  }

  .ais-RefinementList-checkbox:checked, .ais-RefinementList-checkbox:focus, .ais-RefinementList-checkbox:focus-visible {
    @apply bg-sinai-red text-sinai-red
  }
  .ais-RefinementList-labelText {
    @apply flex-1 max-sm:text-[14px] text-sm lg:text-base
  }

  .ais-RefinementList-count {
    @apply text-sm
  }


  /*
   * Hits
   */

  .ais-Hits {
  }

  .ais-Hits-list {
    @apply divide-y divide-black divide-dotted w-full
  }

  .ais-Hits-item {
    @apply py-4 px-4 sm:px-2
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
