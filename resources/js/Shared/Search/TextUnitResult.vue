<template>
  <a :href="'/textunits/' + result.id">
    <div class="flex flex-col lg:grid lg:grid-cols-6 gap-x-1">
        <h3 class="font-medium text-lg lg:text-base text-sinai-dark-blue">{{ result.label }}</h3>
        <p class="text-sm mt-2 lg:mt-0">{{ result.ark }}</p>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>