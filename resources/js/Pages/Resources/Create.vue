<template>
  <AppLayout title="Add Resource">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Resources > Add Resource
            </h1>
          </div>
        </div>
      </div>
    </div>

    <CreateEditForm
      :schema="schema"
      :uischema="uischema"
      :data="data"
      @on-cancel="onCancel"
      @on-save="onSave"
      class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-16"
    />
  </AppLayout>
</template>

<script setup>
  import { defineAsyncComponent, ref } from 'vue'
  import useEmitter from '@/composables/useEmitter'
  import AppLayout from '@/Layouts/AppLayout.vue'
  const CreateEditForm = defineAsyncComponent(() => import('@/jsonforms/components/CreateEditForm.vue'))

  const props = defineProps({
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    saveEndpoint: { type: String, required: true },
    redirectUrl: { type: String, required: true },
  })

  const emitter = useEmitter()

  const data = ref({
    ark: 'ark:/21198/z1.111111',
    type: 'shelf',
    idno: [
      {
        type: 'shelfmark',
        value: 'MS. 123',
      },
    ],
  })

  const onSave = (jsonData) => {
    axios.post(props.saveEndpoint, {
      json: jsonData,
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
