<template>
  <a :href="'/works/' + result.id">
    <div class="flex flex-col lg:grid lg:grid-cols-5 gap-x-1">
        <h3 class="font-medium text-lg lg:text-base">{{ result.pref_title }}</h3>
        <p>{{ result.orig_lang_label }}</p>
        <div>
          <p v-for="genre in result.genre" :key="genre">
            {{ genre }}
          </p>
        </div>
        <p>{{ result.creation_value }}</p>
        <p class="text-sm mt-2 lg:mt-0">{{ result.ark }}</p>
    </div>
  </a>
</template>

<script setup>
  defineProps({
    result: { type: Object, required: true },
  })
</script>