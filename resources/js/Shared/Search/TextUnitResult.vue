<template>
  <a :href="route('frontend.textunits.show', { textunit: result.id })">
    <div class="flex flex-col lg:grid lg:grid-cols-5 gap-x-1">
      <p>
        {{ result.source }}
      </p>

      <h3 class="font-medium text-lg lg:text-base text-sinai-dark-blue">
        {{ result.label }}
      </h3>

      <p>
        {{ result.languages.join(', ') }}
      </p>

      <p>
        {{ result.genres.join(', ') }}
      </p>

      <p class="text-sm">
        {{ result.ark }}
      </p>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>
