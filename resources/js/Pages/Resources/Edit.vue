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

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <JsonForms
        :data="data"
        :renderers="renderers"
        :schema="schema"
        :uischema="uischema"
        @change="onChange"
      />

      <div class="flex justify-end space-x-4 py-8">
        <VBtn
          color="primary"
          @click="onSave(false)">
          Save and Continue
        </VBtn>
      
        <VBtn
          color="primary"
          @click="onSave(true)">
          Save and Finish
        </VBtn>
      </div>
    </div>
  </AppLayout>
</template>

<script>
  import { defineComponent, ref, provide, onMounted } from 'vue'
  import { JsonForms } from '@jsonforms/vue'
  import {
    defaultStyles,
    mergeStyles,
    extendedVuetifyRenderers
  } from '@jsonforms/vue-vuetify'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import useEmitter from '@/composables/useEmitter'

  export default defineComponent({
    name: 'Edit',

    components: {
      AppLayout,
      JsonForms,
    },

    props: {
      schema: { type: Object, required: true },
      uischema: { type: Object, required: true },
      data: { type: Object, required: false, default: null },
      saveEndpoint: { type: String, required: true },
      redirectUrl: { type: String, required: true },
    },

    setup(props) {
      const renderers = Object.freeze([
        ...extendedVuetifyRenderers,
        // custom renderers
      ])

      const emitter = useEmitter()

      const data = ref({})

      onMounted(() => {
        // initialize the form with the supplied metadata
        data.value = props.data ?? {}
      })

      const onChange = (event) => {
        data.value = event.data
      }

      const onSave = (redirect = false) => {
        redirect ? saveAndFinish() : saveAndContinue()
      }

      const saveAndFinish = () => {
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

      const saveAndContinue = () => {
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

      // mergeStyles combines all classes from both styles definitions
      const myStyles = mergeStyles(defaultStyles, {
        control: {
          root: 'my-control'
        }
      })

      provide('styles', myStyles)

      return {
        renderers,
        data,
        onChange,
        onSave,
        myStyles,
      }
    }
  })
</script>
