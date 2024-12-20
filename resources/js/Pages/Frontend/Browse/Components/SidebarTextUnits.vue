<template>
  <template v-if="textUnits && textUnits.length > 0">
    <h3>Text Units</h3>
    <ul>
      <li v-for="textUnit in textUnits">
        <template v-if="textUnit.id">
          <Link :href="route('frontend.textunits.show', { textunit: textUnit.id })">
            {{ textUnit.text_unit.label }}
          </Link>
        </template>
        <template v-else>
          {{ textUnit.label }}
        </template>
      </li>
    </ul>
  </template>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
  components: { Link },
  props: {
    textUnits: { type: Array, required: false, default: () => [] },
  },
};
</script>
