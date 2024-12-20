<template>
  <template v-if="works && works.length > 0">
    <h3>Works</h3>
    <ul>
      <li v-for="work in works">
        <template v-if="work.id">
          <Link :href="route('frontend.works.show', { work: work.id })">
            {{ work.pref_title || '' }}
          </Link>
        </template>
        <template v-else>
          {{ work.value || '' }}
        </template>
      </li>
    </ul>
  </template>
</template>
<script>
import {Link} from "@inertiajs/vue3";

export default {
  components: {Link},
  props: {
    works: {type: Array, required: false, default: () => []},
  },
};
</script>