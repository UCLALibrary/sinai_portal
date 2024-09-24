<template>
  <a :href="'/agents/' + result.id">
    <div class="flex flex-col lg:flex-row">
      <div class="w-full lg:w-4/12">
        <h3 class="font-medium text-lg pr-4 tracking-wide">
          {{ result.pref_name }}
        </h3>
      </div>
  
      <div class="w-full lg:w-2/12">
        <p class="pr-3">
          {{ result.type }}
        </p>
      </div>
  
      <div class="w-full lg:w-1/12">
        <p class="pr-3">
          {{ result.gender }}
        </p>
      </div>

       <div class="w-full lg:flex-1">
        <div class="flex">
          <p>{{ result.birth_value }}</p>
          <p>{{ result.death_value }}</p>
          <p>{{ result.floruit_value }}</p>
        </div>
      </div>
  
      <div class="w-full lg:w-auto">
        <p class="text-sm">
          {{ result.ark }}
        </p>
      </div>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>
