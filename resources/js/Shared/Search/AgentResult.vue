<template>
  <a :href="'/agents/' + result.id">
    <div class="flex">
      <div class="w-[85%] sm:w-[55%] cursor-pointer">
        <h3 class="font-medium max-w-5xl pr-8">
          {{ result.pref_name }}
        </h3>
      </div>
  
      <div class="sm:w-[45%] flex cursor-pointer">
        <p class="pr-3 sm:pr-8">
          {{ result.type }}
        </p>
      </div>
  
      <div class="sm:w-[45%] flex cursor-pointer">
        <p class="pr-3 sm:pr-8">
          {{ result.gender }}
        </p>
      </div>
  
      <div class="sm:w-[45%] flex cursor-pointer">
        <p class="pr-3 sm:pr-8">
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
