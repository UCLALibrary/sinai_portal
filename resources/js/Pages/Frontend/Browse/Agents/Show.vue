<template>
  <FrontendLayout :title="title">
    <h2 class="font-dosis flex text-center justify-center mx-auto text-4xl">
      Agent: {{ agent.pref_name }}
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
