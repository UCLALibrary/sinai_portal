<template>
  <template v-if="names && names.length > 0">
    <h3>Names</h3>
    <ul>
      <li v-for="name in names">
        <template v-if="name.id">
          <Link :href="route('frontend.agents.show', { agent: name.id })">
            {{ name.pref_name || '' }}
          </Link>
        </template>
        <template v-else>
          {{ name.value || '' }}
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
    names: {type: Array, required: false, default: () => []},
  },
};
</script>