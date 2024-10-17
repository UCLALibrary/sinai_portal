<template>
  <AppLayout title="Add Resource">
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
          v-if="uploadEndpoint"
          label="Select a JSON file"
          :multiple="false"
          :endpoint="uploadEndpoint"
          @on-success="onUploadSuccess"
          class="py-4"
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
  import { router } from '@inertiajs/vue3'
  import useEmitter from '@/composables/useEmitter'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import FileUploadForm from '@/Pages/Resources/FileUploadForm.vue'
  const ResourceForm = defineAsyncComponent(() => import('@/jsonforms/components/ResourceForm.vue'))

  const props = defineProps({
    title: { type: String, required: true },
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    saveEndpoint: { type: String, required: true },
    uploadEndpoint: { type: String, required: false, default: null },
    redirectUrl: { type: String, required: true },
  })

  const emitter = useEmitter()

  const onUploadSuccess = (payload) => {
    router.visit(route('manuscripts.edit', payload.resourceId), {
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
    axios.post(props.saveEndpoint, {
      json: payload.data,
    }).then(() => {
      router.visit(props.redirectUrl)
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
    router.visit(props.redirectUrl)
  }
</script>
