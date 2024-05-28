<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
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
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue'
  import {
    ControlElement,
    JsonFormsRendererRegistryEntry,
    rankWith,
    scopeEndIs,
  } from '@jsonforms/core'
  import { VAutocomplete } from 'vuetify/components'
  import {
    rendererProps,
    useJsonFormsControl,
    RendererProps,
  } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'

  const controlRenderer = defineComponent({
    name: 'date-selection-renderer',

    components: {
      ControlWrapper,
      VAutocomplete,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
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
            title: date['as_written'],
            value: date['id'],
          }))
        } catch (error) {
          dates.value = []
        }
      }

      return {
        ...rest,
        dates,
        onFocus,
      }
    }
  })

  export default controlRenderer

  export const entry: JsonFormsRendererRegistryEntry = {
    renderer: controlRenderer,
    tester: rankWith(4, scopeEndIs('assoc_date')),
  }
</script>
