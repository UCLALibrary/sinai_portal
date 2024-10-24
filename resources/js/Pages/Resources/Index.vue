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
              @click="router.visit(route($page.props.routes.create, $page.props.resourceName))"
              class="block rounded-md bg-sinai-red px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-sinai-red focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sinai-red">
              Create
            </button>
          </div>
        </div>

        <FileUploadForm
          v-if="!config.disable_file_uploads && $page.props.routes.upload && $page.props.routes.upload.batch"
          label="Select one or more JSON files"
          hint="Note: The uploaded files will overwrite any existing data. Any file with an ark that doesn't match an existing record will create a new record."
          :multiple="true"
          :endpoint="route($page.props.routes.upload.batch, $page.props.resourceName)"
          @on-success="onUploadSuccess"
          class="px-4 sm:px-6 lg:px-8 py-4"
        />

        <ResourceListTable
          :resources="resources.data"
          :columns="config.index.columns"
          :pagination="{
            from: resources.from ?? 0,
            to: resources.to ?? 0,
            total: resources.total,
            links: resources.links
          }"
          class="px-4 sm:px-6 lg:px-8 my-8"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import FileUploadForm from '@/Pages/Resources/FileUploadForm.vue'
  import ResourceListTable from '@/Shared/ResourceListTable.vue'
  import useEmitter from '@/composables/useEmitter'

  defineProps({
    title: { type: String, required: true },
    resources: { type: Object, required: false, default: () => {} },
    config: { type: Object, required: false, default: null },
  })
  
  const emitter = useEmitter()

  const onUploadSuccess = (payload) => {
    router.visit(window.location.href, {
      onSuccess: () => {
        // display alert that the resource has been saved
        emitter.emit('show-dismissable-alert', {
          type: payload.status,
          message: payload.message
        })
      },
    })
  }

</script>