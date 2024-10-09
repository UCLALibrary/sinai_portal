<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <el-select-v2
      :model-value="control.data"
      :options="records"
      placeholder="Select one or more locations"
      multiple
      clearable
      collapse-tags
      collapse-tags-tooltip
      :max-collapse-tags="4"
      @update:modelValue="onChange"
      @focus="onFocus"
    />
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsControl } from '@jsonforms/vue'
  import { useCustomVanillaControl } from '@/jsonforms/util'
  import { default as ControlWrapper } from '../controls/MyInlineControlWrapper.vue'
  import axios from 'axios'
  import useEmitter from '@/composables/useEmitter'

  const controlRenderer = defineComponent({
    name: 'MyLocationSelectionRenderer',

    components: {
      ControlWrapper,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
      const emitter = useEmitter()

      const control = useCustomVanillaControl(useJsonFormsControl(props))

      const records = ref([])

      onMounted(() => {
        fetchRecords()
      })

      const fetchRecords = async () => {
        try {
          const response = await axios.get(route('api.locations.index'))
          records.value = response.data.data.map((record) => ({
            label: record['repository'] + ' ' + record['collection'],
            value: record['id'],
          }))
        } catch (error) {
          records.value = []
        }
      }

      const onFocus = () => {
        // fetch the latest set of records when the control is focused to ensure the list of options is up to date
        fetchRecords()
      }

      return {
        ...control,
        records,
        onFocus
      }
    }
  })

  export default controlRenderer
</script>
