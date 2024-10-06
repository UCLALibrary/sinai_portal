<template>
  <a :href="'/agents/' + result.id">
    <div class="flex flex-col lg:grid lg:grid-cols-5 gap-x-1">
      <h3 class="font-medium pr-4 text-sinai-dark-blue">{{ result.pref_name }}</h3>
      <p class="pr-3">{{ result.type }}</p>
      <p class="pr-3">{{ result.gender || 'N/A' }}</p>

      <div class="flex flex-col">
        <p>{{ result.birth_value }}</p>
        <p>{{ result.death_value }}</p>
        <p>{{ result.floruit_value }}</p>
      </div>

      <p class="text-sm">{{ result.ark }}</p>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>
