<template>
  <template v-for="textUnit in textUnits">
    <p class="mb-8">
      <strong>
        <template v-if="textUnit.id">
          <Link :href="route('frontend.textunits.show', { textunit: textUnit.id })">
            {{ textUnit.label }}
          </Link>
        </template>
        <template v-else>
          {{ textUnit.label }}
        </template>
      </strong>
      <template v-if="textUnit.locus && textUnit.locus !== ''">
        ({{ textUnit.locus }})
      </template>
      <span class="block" v-if="textUnit.text_unit?.lang && textUnit.text_unit.lang.length > 0">
        Languages: {{ textUnit.text_unit.lang.map(lang => lang.label).join(' | ') }}
      </span>
      <span class="block" v-if="textUnit.text_unit?.work_wit && textUnit.text_unit.work_wit.length > 0">
        Works: {{
            textUnit.text_unit.work_wit
                .map(work => work.pref_title || work.desc_title || '')
                .filter(title => title)
                .join(' | ')
          }}
      </span>
    </p>
  </template>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
  components: { Link },
  props: {
    textUnits: { type: Array, required: true },
  },
};
</script>
