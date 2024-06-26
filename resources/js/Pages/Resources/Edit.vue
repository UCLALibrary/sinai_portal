<template>
  <AppLayout title="Add Resource">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Resources > Edit Resource
            </h1>
          </div>
        </div>
      </div>
    </div>

    <CreateEditForm
      :schema="schema"
      :uischema="uischema"
      :data="data"
      @on-save="onSave"
      @on-save-and-continue="onSaveAndContinue"
      class="max-w-7xl mx-auto sm:px-6 lg:px-8"
    />
  </AppLayout>
</template>

<script>
  import { defineComponent, defineAsyncComponent, ref, onMounted } from 'vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  const CreateEditForm = defineAsyncComponent(() => import('@/jsonforms/components/CreateEditForm.vue'))
  import useEmitter from '@/composables/useEmitter'

  export default defineComponent({
    name: 'Edit',

    components: {
      AppLayout,
      CreateEditForm,
    },

    props: {
      schema: { type: Object, required: true },
      uischema: { type: Object, required: true },
      data: { type: Object, required: false, default: null },
      saveEndpoint: { type: String, required: true },
      redirectUrl: { type: String, required: true },
    },

    setup(props) {
      const emitter = useEmitter()

      const data = ref({})

      onMounted(() => {
        // initialize the form with the supplied metadata
        data.value = props.data ?? {}
      })

      const onChange = (event) => {
        data.value = event.data
      }

      const onSave = () => {
        axios.patch(props.saveEndpoint, {
          json: data.value,
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

      const onSaveAndContinue = () => {
        axios.patch(props.saveEndpoint, {
          json: data.value,
        }).then(() => {
          // display alert that the resource has been saved
          emitter.emit('show-dismissable-alert', {
            type: 'success',
            message: 'Saved successfully. Please continue editing.',
            timeout: 2000,
          })
        }).catch(error => {
          // display alert that there was an error saving the resource
          emitter.emit('show-dismissable-alert', {
            type: 'error',
            message: 'Error saving. Please check your form for errors.',
            timeout: 2000,
          })
        })
      }

      return {
        data,
        onChange,
        onSave,
        onSaveAndContinue,
      }
    }
  })
</script>
