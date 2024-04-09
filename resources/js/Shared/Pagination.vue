<template>
  <div v-if="links.length > 3" class="flex flex-col gap-y-8 py-8 sm:flex-row sm:justify-between sm:items-center">
    <p class="text-sm text-gray-700">
      Showing
      {{ ' ' }}
      <span class="font-medium">{{ from }}</span>
      {{ ' ' }}
      to
      {{ ' ' }}
      <span class="font-medium">{{ to }}</span>
      {{ ' ' }}
      of
      {{ ' ' }}
      <span class="font-medium">{{ total }}</span>
      {{ ' ' }}
      results
    </p>

    <div class="flex flex-wrap">
      <template v-for="(link, key) in links">
        <div
          v-if="link.url === null"
          :key="key"
          class="mb-1 mr-1 px-4 py-3 text-gray-400 text-sm leading-4 border rounded"
          v-html="link.label"
        />
        <Link
          v-else
          :key="`link-${key}`"
          class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded"
          :class="{ 'bg-white': link.active }"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3'

  defineProps({
    from: { type: Number, required: true },
    to: { type: Number, required: true },
    total: { type: Number, required: true },
    links: { type: Array, required: false, default: () => [] },
  })
</script>
