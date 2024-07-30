<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <el-select-v2
      :model-value="control.data"
      :options="resources"
      placeholder="Select one or more parts to attach"
      multiple
      clearable
      collapse-tags
      collapse-tags-tooltip
      :max-collapse-tags="4"
      @update:modelValue="onChange"
      @focus="onFocus">
      <template #tag>
        <el-tag
          v-for="resourceId in control.data"
          :key="resourceId"
          closable
          disable-transitions
          @click.stop
          class="cursor-auto">
          <div>
            {{ getResourceLabelById(resourceId) }}
          </div>

          <ShowResourceButton
            title="Show Part"
            :form-endpoint="route('api.forms.codicological-units', { id: resourceId })"
            styles="ml-2 hover:bg-sinai-red hover:text-white"
          />

          <EditResourceButton
            title="Edit Part"
            :resource-id="resourceId"
            :form-endpoint="route('api.forms.codicological-units', { id: resourceId })"
            @on-save="onUpdate"
            styles="ml-1 hover:bg-sinai-red hover:text-white"
          />
        </el-tag>
      </template>
    </el-select-v2>

    <template v-slot:actions>
      <CreateResourceButton
        title="Create Part"
        :styles="styles.arrayList.addButton"
        :form-endpoint="route('api.forms.codicological-units')"
        @on-save="onCreate"
      />
    </template>
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
  import ShowResourceButton from '@/jsonforms/components/ShowResourceButton.vue'
  import EditResourceButton from '@/jsonforms/components/EditResourceButton.vue'

  const controlRenderer = defineComponent({
    name: 'MyPartSelectionRenderer',

    components: {
      ControlWrapper,
      CreateResourceButton,
      ShowResourceButton,
      EditResourceButton,
    },

    props: {
      ...rendererProps<ControlElement>()
    },

    setup(props: RendererProps<ControlElement>) {
      const emitter = useEmitter()

      const control = useCustomVanillaControl(useJsonFormsControl(props))

      const resources = ref([])

      onMounted(() => {
        fetchResources()
      })

      const fetchResources = async () => {
        try {
          const response = await axios.get(route('api.codicological-units.index'))
          resources.value = response.data.data.map((resource) => ({
            id: resource['id'],
            label: resource['identifier'],
            value: resource['id'],
          }))
        } catch (error) {
          resources.value = []
        }
      }

      const getResourceLabelById = (id) => {
        const resource = resources.value.find((resource) => resource.id === id)
        return resource ? resource.label : ''
      }

      const onFocus = () => {
        // fetch the latest set of resources when the control is focused to ensure the list of selectable options is up to date
        fetchResources()
      }

      const onCreate = (jsonData) => {
        axios.post(route('api.codicological-units.store'), {
          json: jsonData,
        }).then(response => {
          // fetch the latest set of resources so the new resource can be attached via its id
          fetchResources()

          // attach the new resource by appending its id to the control data
          if (!control.control.value.data) {
            control.control.value.data = []
          }
          control.control.value.data.push(response.data.data['id'])
        }).catch(error => {
          // display alert that there was an error saving the resource
          emitter.emit('show-dismissable-alert', {
            type: 'error',
            message: 'Error saving. Please try again.',
            timeout: 2000,
          })
        })
      }

      const onUpdate = (payload) => {
        axios.put(route('api.codicological-units.update', { id: payload.resourceId }), {
          json: payload.data,
        }).then(_ => {
          // fetch the latest set of resources to update thelabel for attached resources
          fetchResources()

          // display alert that there the resource was saved successfully
          emitter.emit('show-dismissable-alert', {
            type: 'success',
            message: 'Saved successfully. Please continue editing.',
            timeout: 2000,
          })
        }).catch(_ => {
          // display alert that there was an error saving the resource
          emitter.emit('show-dismissable-alert', {
            type: 'error',
            message: 'Error saving. Please try again.',
            timeout: 2000,
          })
        })
      }

      return {
        ...control,
        resources,
        getResourceLabelById,
        onFocus,
        onCreate,
        onUpdate,
      }
    }
  })

  export default controlRenderer
</script>
