<!-- 
  The <ais-infinite-hits> Vue InstantSearch widget displays a list of results with a 
  "Show more" button at the bottom of the list. Using the <ais-infinite-hits> widget 
  is inadequate since our use case requires an infinite scroll implementation.

  To create an automatically-scrolling infinite hits experience, Algolia provides an 
  "infinite scroll guide".

  See https://www.algolia.com/doc/guides/building-search-ui/ui-and-ux-patterns/infinite-scroll/vue/

  After several unsuccessful attempts to implement infinite scroll using their recommended 
  use of the vue-observe-visibility library, this custom <infinite-hits> component was 
  implemented instead using the Intersection Observer API.
  
  This component uses a mixin to connect to the Algolia InstantSearch API which is
  implemented using Vue 2 Options API syntax. Refactoring this component into Vue 3
  Composition API syntax is discouraged.
-->

<template>
  <div v-if="state && state.hits.length > 0" class="ais-Hits">
    <ol class="ais-Hits-list">
      <li v-for="hit in state.hits" :key="hit.objectID" class="ais-Hits-item">
        <slot name="item" :item="hit"></slot>
      </li>
      <li class="sentinel" v-observe-visibility="visibilityChanged">
        <slot name="loading-indicator" />
      </li>
    </ol>
  </div>
  <div v-else-if="state && state.hits.length === 0" class="px-2 py-4">
    No results found
  </div>
</template>

<script>
  import { createWidgetMixin } from 'vue-instantsearch/vue3/es'
  import { connectInfiniteHits } from 'instantsearch.js/es/connectors'

  export default {
    mixins: [createWidgetMixin({ connector: connectInfiniteHits })],

    methods: {
      visibilityChanged(isVisible) {
        if (isVisible && this.state && !this.state.isLastPage) {
          this.state.showMore()
        }
      }
    }
  }
</script>

<style lang="postcss" scoped>
  .ais-Hits-item:empty {
    @apply hidden
  }
</style>