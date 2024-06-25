<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <v-autocomplete
      label="Parts"
      :items="parts"
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
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, useJsonFormsControl, RendererProps } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'
  import { VAutocomplete } from 'vuetify/components'

  const controlRenderer = defineComponent({
    name: 'part-selection-renderer',

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

      const parts = ref([])

      onMounted(() => {
        fetchParts()
      })

      const onFocus = () => {
        // fetch the latest set of options when the control is focused to ensure the list is up to date
        fetchParts()
      }

      const fetchParts = async () => {
        try {
          const response = await axios.get('/api/codicological-units')
          parts.value = response.data.data.map((part) => ({
            ...part,
            title: part['identifier'],
            value: part['id'],
          }))
        } catch (error) {
          parts.value = []
        }
      }

      return {
        ...rest,
        parts,
        onFocus,
      }
    }
  })

  export default controlRenderer
</script>

