<template>
  <a :href="route('frontend.textunits.show', { textunit: result.id })">
    <div class="flex flex-col lg:grid lg:grid-cols-5 gap-x-1">
      <p>
        {{ result.source }}
      </p>

      <h3 class="font-medium text-lg lg:text-base text-sinai-dark-blue">
        {{ result.label }}
      </h3>

      <div>
        <p v-for="language in result.languages" :key="language">
          {{ language }}
        </p>
      </div>

      <div>
        <p v-for="genre in result.genres" :key="genre">
          {{ genre }}
        </p>
      </div>

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
