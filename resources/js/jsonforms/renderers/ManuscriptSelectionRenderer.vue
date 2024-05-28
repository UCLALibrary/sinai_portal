<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <v-select
      label="Parent Manuscript"
      :items="manuscripts"
      clearable
      variant="outlined"
      :model-value="control.data"
      @update:modelValue="onChange"
      @update:focused="onFocus">
    </v-select>
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
  import { VSelect } from 'vuetify/components'
  import {
    rendererProps,
    useJsonFormsControl,
    RendererProps,
  } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'

  const controlRenderer = defineComponent({
    name: 'manuscript-selection-renderer',

    components: {
      ControlWrapper,
      VSelect,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
      const control = useJsonFormsControl(props)
      const { handleChange, ...rest } = useVuetifyControl(control)

      const manuscripts = ref([])

      onMounted(() => {
        fetchManuscripts()
      })

      const onFocus = () => {
        // fetch the latest set of options when the control is focused to ensure the list is up to date
        fetchManuscripts()
      }

      const fetchManuscripts = async () => {
        try {
          const response = await axios.get('/api/manuscripts')
          manuscripts.value = response.data.data.map((manuscript) => ({
            ...manuscript,
            title: manuscript['shelfmark'],
            value: manuscript['id'],
          }))
        } catch (error) {
          manuscripts.value = []
        }
      }

      return {
        ...rest,
        manuscripts,
        onFocus,
      }
    }
  })

  export default controlRenderer

  export const entry: JsonFormsRendererRegistryEntry = {
    renderer: controlRenderer,
    tester: rankWith(4, scopeEndIs('ms_objs')),
  }
</script>
