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
          v-if="$page.props.routes.upload && $page.props.routes.upload.update"
          label="Select a JSON file"
          :multiple="false"
          hint="Note: The uploaded file will overwrite the existing data"
          :endpoint="route($page.props.routes.upload.update, { resourceName: $page.props.resourceName, resourceId: resource.id })"
          @on-success="onUploadSuccess"
          @on-error="onUploadError"
          class="px-4 sm:px-6 lg:px-8 py-4"
        />

        <ResourceForm
          :schema="schema"
          :uischema="uischema"
          :data="data"
          mode="edit"
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
    resource: { type: Object, required: true },
    resourceName: { type: String, required: true },
  })

  const emitter = useEmitter()

  const page = usePage();

  const onUploadSuccess = (payload) => {
    router.visit(window.location.href, {
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

  const onUploadError = (payload) => {
    // display alert that there was an error saving the resource
    emitter.emit('show-dismissable-alert', {
      type: payload.status,
      message: payload.message,
    })
  }

  const onSave = (payload) => {
    axios.put(route(page.props.routes.update, { resourceName: props.resourceName, resourceId: props.resource.id }), {
      json: payload.data,
    }).then(() => {
      if (payload.continueEditing) {
        // display alert that the resource has been saved
        emitter.emit('show-dismissable-alert', {
          type: 'success',
          message: 'Saved successfully. Please continue editing.',
          timeout: 2000,
        })
      } else {
        router.visit(route(page.props.routes.index, props.resourceName))
      }
    }).catch(error => {
      // display alert that there was an error saving the resource
      emitter.emit('show-dismissable-alert', {
        type: 'error',
        message: error,
        timeout: 2000,
      })
    })
  }

  const onCancel = () => {
    router.visit(route(props.routes.index))
  }
</script>
