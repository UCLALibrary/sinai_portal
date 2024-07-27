<template>
  <AppLayout title="Add Resource">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              {{ title }}
            </h1>
          </div>
        </div>

        <ResourceForm
          :schema="schema"
          :uischema="uischema"
          :data="data || {}"
          @on-save="onSave"
          @on-cancel="onCancel"
          class="px-4 sm:px-6 lg:px-8 mb-16"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { defineAsyncComponent } from 'vue'
  import useEmitter from '@/composables/useEmitter'
  import AppLayout from '@/Layouts/AppLayout.vue'
  const ResourceForm = defineAsyncComponent(() => import('@/jsonforms/components/ResourceForm.vue'))

  const props = defineProps({
    title: { type: String, required: true },
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    saveEndpoint: { type: String, required: true },
    redirectUrl: { type: String, required: true },
  })

  const emitter = useEmitter()

  const onSave = (payload) => {
    axios.post(props.saveEndpoint, {
      json: payload.data,
    }).then(() => {
      window.location.href = props.redirectUrl
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
    window.location.href = props.redirectUrl
  }
</script>
