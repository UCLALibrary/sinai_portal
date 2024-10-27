<template>
  <AppLayout :title="title">
    <div class="lg:py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              {{ title }}
            </h1>
          </div>
        </div>

        <FileUploadForm
          v-if="page.props.routes.upload && page.props.routes.upload.store"
          label="Select a JSON file"
          :multiple="false"
          :endpoint="route(page.props.routes.upload.store, page.props.resourceName)"
          @on-success="onUploadSuccess"
          class="px-4 sm:px-6 lg:px-8 py-4"
        />

        <ResourceForm
          :schema="schema"
          :uischema="uischema"
          :data="data || {}"
          mode="create"
          @on-save="onSave"
          @on-cancel="onCancel"
          class="px-4 sm:px-6 lg:px-8 my-8"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { defineAsyncComponent } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  import useEmitter from '@/composables/useEmitter'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import FileUploadForm from '@/Pages/Resources/FileUploadForm.vue'
  const ResourceForm = defineAsyncComponent(() => import('@/jsonforms/components/ResourceForm.vue'))

  const props = defineProps({
    title: { type: String, required: true },
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    resourceName: { type: String, required: true },
  })

  const emitter = useEmitter()

  const page = usePage();

  const onUploadSuccess = (payload) => {
    router.visit(route(page.props.routes.edit, { resourceName: page.props.resourceName, resourceId: payload.resourceId }), {
      onSuccess: () => {
        // display alert that the resource has been saved
        emitter.emit('show-dismissable-alert', {
          type: payload.status,
          message: payload.message,
          timeout: 4000,
        })
      },
    })
  }

  const onSave = (payload) => {
    axios.post(route(page.props.routes.store, page.props.resourceName), {
      json: payload.data,
    }).then(() => {
      router.visit(route($page.props.routes.index))
    }).catch(error => {
      // display alert that there was an error saving the resource
      emitter.emit('show-dismissable-alert', {
        type: 'error',
        message: 'Error saving. Please check your form for errors.',
        timeout: 2000,
      })
    })
  }

  const onCancel = () => {
    router.visit(route($page.props.routes.index))
  }
</script>
