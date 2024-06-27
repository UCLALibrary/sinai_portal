<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions"
    class="flex items-center space-x-4">
    <v-autocomplete
      label="Associated Dates"
      :items="dates"
      chips
      multiple
      clearable
      variant="outlined"
      :model-value="control.data"
      @update:modelValue="onChange"
      @update:focused="onFocus">
    </v-autocomplete>

    <CreateEditFormModalDialog
      title="Create Date"
      :content-endpoint="route('api.forms.assoc_date')"
      @on-save="onSave"
    />
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, useJsonFormsControl, RendererProps } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'
  import axios from 'axios'
  import useEmitter from '@/composables/useEmitter'
  import { VAutocomplete } from 'vuetify/components'
  import CreateEditFormModalDialog from '@/jsonforms/components/CreateEditFormModalDialog.vue'

  const controlRenderer = defineComponent({
    name: 'date-selection-renderer',

    components: {
      ControlWrapper,
      VAutocomplete,
      CreateEditFormModalDialog,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
      const emitter = useEmitter()

      const control = useJsonFormsControl(props)
      const { handleChange, ...rest } = useVuetifyControl(control)

      const dates = ref([])

      onMounted(() => {
        fetchDates()
      })

      const onFocus = () => {
        // fetch the latest set of options when the control is focused to ensure the list is up to date
        fetchDates()
      }

      const fetchDates = async () => {
        try {
          const response = await axios.get('/api/dates')
          dates.value = response.data.data.map((date) => ({
            ...date,
            title: [date['as_written'], date['not_before'], date['not_after'], date['type']].join(' • '),
            value: date['id'],
          }))
        } catch (error) {
          dates.value = []
        }
      }

      const onSave = (jsonData) => {
        axios.post('/api/dates', {
          json: jsonData,
        }).then(() => {
          fetchDates()
        }).catch(error => {
          // display alert that there was an error saving the resource
          emitter.emit('show-dismissable-alert', {
            type: 'error',
            message: 'Error saving date. Please try again.',
            timeout: 2000,
          })
        })
      }

      return {
        ...rest,
        dates,
        onFocus,
        onSave,
      }
    }
  })

  export default controlRenderer
</script>

<style lang="postcss" scoped>
  :deep(.v-autocomplete .v-input__details) {
    @apply hidden
  }
</style>
