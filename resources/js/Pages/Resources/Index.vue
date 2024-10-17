<template>
  <AppLayout :title="title">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              {{ title }}
            </h1>
          </div>
          <div class="sm:ml-16 sm:mt-0 sm:flex-none">
            <button
              type="button"
              @click="redirectToUrl(createEndpoint)"
              class="block rounded-md bg-sinai-red px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-sinai-red focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sinai-red">
              Create
            </button>
          </div>
        </div>

        <ResourceListTable
          :resource-name="resourceName"
          :resources="resources.data"
          :columns="columns"
          :pagination="{
            from: resources.from ?? 0,
            to: resources.to ?? 0,
            total: resources.total,
            links: resources.links
          }"
          class="px-4 sm:px-6 lg:px-8 pb-32"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ResourceListTable from '@/Shared/ResourceListTable.vue'

  defineProps({
    title: { type: String, required: true },
    resourceName: { type: String, required: true },
    resources: { type: Object, required: false, default: () => {} },
    columns: { type: Object, required: true },
    createEndpoint: { type: String, required: true },
  })

  const redirectToUrl = (url) => {
    router.visit(url)
  }
</script>