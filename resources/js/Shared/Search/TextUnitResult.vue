<template>
  <a :href="route('frontend.textunits.show', { textunit: result.id })">
    <div class="flex flex-col lg:grid lg:grid-cols-6 gap-x-1">
      <h3 class="col-span-2 font-medium text-lg lg:text-base text-sinai-dark-blue">
        {{ result.label }}
      </h3>

      <p class="col-span-2">
        {{ result.source }}
      </p>

      <div>
        <p v-for="language in result.languages" :key="language">
          {{ language }}
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
