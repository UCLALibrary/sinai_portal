<template>
  <template v-if="features && features.length > 0">
    <h3>Keywords</h3>
    <ul>
      <li v-for="keyword in features">
        <Link :href="`${route(routeName)}?filters=${encodeURIComponent(JSON.stringify(['features:' + keyword.label]))}`">
          {{ keyword.label }}
        </Link>
      </li>
    </ul>
  </template>
</template>
<script>
import {Link} from "@inertiajs/vue3";
export default {
  components: {Link},
  props: {
    routeName: {type: String, required: true},
    features: {type: Array, required: false, default: []},
  },
};
</script>