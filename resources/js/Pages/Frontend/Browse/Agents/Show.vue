<template>
  <FrontendLayout :title="title">
      <h2 class="font-dosis flex mx-auto text-3xl border-b pb-2">
        {{ agent.pref_name }}
    </h2>
  </FrontendLayout>
</template>

<script setup>
  import FrontendLayout from '@/Layouts/FrontendLayout.vue'

  defineProps({
    title: { type: String, required: true },
    agent: { type: Object, required: true },
  })
</script>
