<template>
  <a :href="'/agents/' + result.id">
    <div class="flex flex-col lg:flex-row">
      <div class="w-full w-[85%] sm:w-[55%] cursor-pointer">
        <h3 class="font-medium max-w-5xl pr-4">
          {{ result.pref_name }}
        </h3>
      </div>
  
      <div class="w-full sm:w-[25%] cursor-pointer">
        <p class="pr-3 sm:pr-4">
          {{ result.type }}
        </p>
      </div>
  
      <div class="w-full sm:w-[15%] cursor-pointer">
        <p class="pr-3 sm:pr-4">
          {{ result.gender }}
        </p>
      </div>

       <div class="w-full sm:w-[45%] cursor-pointer">
        <p class="flex pr-3 sm:pr-4">
          <div>{{ result.birth_value }}</div>
          <div>{{ result.death_value }}</div>
          <div>{{ result.floruit_value }}</div>
        </p>
      </div>
  
      <div class="w-full sm:w-[45%] cursor-pointer">
        <p class="pr-3 sm:pr-4 text-sm">
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
