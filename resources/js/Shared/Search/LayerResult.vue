<template>
  <a :href="'/layers/' + result.id">
    <div class="flex flex-col lg:grid lg:grid-cols-5 gap-x-1">
        <h3 class="font-medium text-lg lg:text-base text-sinai-dark-blue">{{ result.source }}</h3>
        <p>{{ result.identifier }}</p>
        <p>{{ result.extent }}</p>
        <p>{{ result.state }}</p>
        <p class="text-sm mt-2 lg:mt-0">{{ result.ark }}</p>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>