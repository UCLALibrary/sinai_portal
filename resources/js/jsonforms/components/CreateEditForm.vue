<template>
  <div class="flex flex-col">
    <JsonForms
      :data="jsonData"
      :renderers="renderers"
      :schema="schema"
      :uischema="uischema"
      @change="onChange"
      class="create-edit-form"
    />

    <v-container>
      <v-row>
        <v-col class="flex justify-end space-x-4">
          <v-btn
            color="red"
            @click="onCancel">
            Cancel
          </v-btn>

          <v-btn
            color="gray"
            @click="onSave"
            :style="!isValid ? { cursor: 'not-allowed', 'pointer-events': 'auto' } : {}"
            :disabled="!isValid">
            Save
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  import { defineComponent, ref, provide } from 'vue'
  import { JsonForms } from '@jsonforms/vue'
  import {
    defaultStyles,
    mergeStyles,
    extendedVuetifyRenderers
  } from '@jsonforms/vue-vuetify'
  import {
    customStringControlRendererEntry,
    manuscriptSelectionRendererEntry,
    partSelectionRendererEntry,
    dateSelectionRendererEntry,
  } from '@/jsonforms/renderers/useRenderers.js'

  export default defineComponent({
    name: 'CreateEditForm',

    components: {
      JsonForms
    },

    props: {
      schema: { type: Object, required: true },
      uischema: { type: Object, required: true },
      data: { type: Object, required: false, default: () => {} },
    },

    setup(props, { emit }) {
      const renderers = Object.freeze([
        ...extendedVuetifyRenderers,
        // custom renderers
        customStringControlRendererEntry,
        manuscriptSelectionRendererEntry,
        partSelectionRendererEntry,
        dateSelectionRendererEntry,
      ])

      provide('styles', mergeStyles(defaultStyles, {}))

      const jsonData = ref(props.data)

      const isValid = ref(false)

      const onChange = (payload) => {
        jsonData.value = payload.data
        isValid.value = payload.errors.length === 0
      }

      const onSave = () => {
        emit('on-save', jsonData.value)
      }
      
      const onCancel = () => {
        emit('on-cancel')
      }

      return {
        renderers,
        jsonData,
        isValid,
        onChange,
        onSave,
        onCancel,
      }
    }
  })
</script>
