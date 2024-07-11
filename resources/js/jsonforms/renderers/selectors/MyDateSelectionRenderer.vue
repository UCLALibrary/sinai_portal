<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <el-select-v2
      :model-value="control.data"
      :options="dates"
      placeholder="Please select"
      multiple
      clearable
      collapse-tags
      collapse-tags-tooltip
      :max-collapse-tags="4"
      @update:modelValue="onChange"
      @focus="onFocus"
    />

    <template v-slot:actions>
      <button type="button" :class="styles.arrayList.addButton"></button>
    </template>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent, ref, onMounted } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsControl } from '@jsonforms/vue'
  import { useCustomVanillaControl } from '@/jsonforms/util'
  import { default as ControlWrapper } from '../controls/MyControlWrapper.vue'
  import axios from 'axios'
  import useEmitter from '@/composables/useEmitter'
  import { ElSelectV2 } from 'element-plus'
  import CreateEditFormModalDialog from '@/jsonforms/components/CreateEditFormModalDialog.vue'

  const controlRenderer = defineComponent({
    name: 'MyDateSelectionRenderer',

    components: {
      ControlWrapper,
      ElSelectV2,
      CreateEditFormModalDialog,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
      const emitter = useEmitter()

      const control = useCustomVanillaControl(useJsonFormsControl(props))

      const dates = ref([])

      onMounted(() => {
        fetchDates()
      })

      const fetchDates = async () => {
        try {
          const response = await axios.get('/api/dates')
          dates.value = response.data.data.map((date) => ({
            label: [date['as_written'], date['not_before'], date['not_after'], date['type']].join(' • '),
            value: date['id'],
          }))
        } catch (error) {
          dates.value = []
        }
      }

      const onFocus = () => {
        // fetch the latest set of dates when the control is focused to ensure the list of options is up to date
        fetchDates()
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
        ...control,
        dates,
        onFocus,
        onSave,
      }
    }
  })

  export default controlRenderer
</script>

<style lang="postcss" scoped>
  .el-select {
    @apply flex-1
  }
</style>
