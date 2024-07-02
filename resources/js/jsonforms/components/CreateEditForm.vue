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

          <template v-if="mode == 'create'">
            <v-btn
              color="gray"
              @click="onSave(false)"
              :style="!isValid ? { cursor: 'not-allowed', 'pointer-events': 'auto' } : {}"
              :disabled="!isValid">
              Save
            </v-btn>
          </template>
          <template v-else-if="mode == 'edit'">
            <v-btn
              color="gray"
              @click="onSave(true)"
              :style="!isValid ? { cursor: 'not-allowed', 'pointer-events': 'auto' } : {}"
              :disabled="!isValid">
              Save and Continue
            </v-btn>
          
            <v-btn
              color="gray"
              @click="onSave(false)"
              :style="!isValid ? { cursor: 'not-allowed', 'pointer-events': 'auto' } : {}"
              :disabled="!isValid">
              Save and Finish
            </v-btn>
          </template>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
  import { ref, provide } from 'vue'
  import { JsonForms } from '@jsonforms/vue'
  import {
    defaultStyles,
    mergeStyles,
    extendedVuetifyRenderers
  } from '@jsonforms/vue-vuetify'
  import {
    customStringControlRendererEntry,
    // customEnumControlRendererEntry,
    manuscriptSelectionRendererEntry,
    partSelectionRendererEntry,
    dateSelectionRendererEntry,
  } from '@/jsonforms/renderers/useRenderers.js'

  const emit = defineEmits(['on-save', 'on-cancel']);

  const props = defineProps({
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    mode: { type: String, required: false, default: 'create' },
  })

  const renderers = Object.freeze([
    ...extendedVuetifyRenderers,
    // custom renderers
    customStringControlRendererEntry,
    // customEnumControlRendererEntry,
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

  const onSave = (continueEditing) => {
    emit('on-save', {
      data: jsonData.value,
      continueEditing: continueEditing
    })
  }
  
  const onCancel = () => {
    emit('on-cancel')
  }
</script>
