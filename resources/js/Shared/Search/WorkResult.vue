<template>
  <a :href="'/works/' + result.id">
    <div class="flex flex-col lg:flex-row">
      <div class="w-full lg:w-4/12">
        <h3 class="font-medium text-lg pr-4 tracking-wide">
          {{ result.pref_title }}
        </h3>
      </div>
  
      <div class="w-full lg:w-1/12">
        <p class="pr-3">
          {{ result.orig_lang_label }}
        </p>
      </div>
  
      <div class="w-full lg:w-3/12">
        <p class="pr-3">
          {{ result.genre.join('; ') }}
        </p>
      </div>

      <div class="w-full lg:flex-1">
        <p class="pr-3">
          {{ result.creation_value }}
        </p>
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
