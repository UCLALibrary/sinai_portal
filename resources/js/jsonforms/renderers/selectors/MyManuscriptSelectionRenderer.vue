<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <el-select-v2
      :model-value="control.data"
      :options="records"
      placeholder="Select one or more manuscripts"
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
  import CreateResourceButton from '@/jsonforms/components/CreateResourceButton.vue'

  const controlRenderer = defineComponent({
    name: 'MyManuscriptSelectionRenderer',

    components: {
      ControlWrapper,
      CreateResourceButton,
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
          const response = await axios.get(route('api.manuscripts.index'))
          records.value = response.data.data.map((record) => ({
            label: record['identifier'],
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

      const onSave = (jsonData) => {
        axios.post(route('api.manuscripts.store'), {
          json: jsonData,
        }).then(response => {
          // fetch the latest set of records so the new record can be attached via its id
          fetchRecords()

          // attach the new record by appending its id to the control data
          if (!control.control.value.data) {
            control.control.value.data = []
          }
          control.control.value.data.push(response.data.data['id'])
        }).catch(error => {
          // display alert that there was an error saving the record
          emitter.emit('show-dismissable-alert', {
            type: 'error',
            message: 'Error saving. Please try again.',
            timeout: 2000,
          })
        })
      }

      return {
        ...control,
        records,
        onFocus,
        onSave,
      }
    }
  })

  export default controlRenderer
</script>
