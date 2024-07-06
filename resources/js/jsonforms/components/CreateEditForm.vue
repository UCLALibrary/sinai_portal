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
          <button
            type="button"
            @click="onCancel"
            class="cancel-button">
            Cancel
          </button>

          <template v-if="mode == 'create'">
            <button
              type="button"
              @click="onSave(false)"
              class="save-button"
              :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
              :disabled="!isValid">
              Save
            </button>
          </template>
          <template v-else-if="mode == 'edit'">
            <button
              type="button"
              @click="onSave(true)"
              class="save-button"
              :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
              :disabled="!isValid">
              Save &amp; Continue
            </button>

            <button
              type="button"
              @click="onSave(false)"
              class="save-button"
              :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
              :disabled="!isValid">
              Save &amp; Finish
            </button>
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
    vanillaRenderers
  } from '@jsonforms/vue-vanilla'
  import {
    myStringControlRendererEntry,
  } from '@/jsonforms/renderers/useCustomRenderers.js'

  const emit = defineEmits(['on-save', 'on-cancel']);

  const props = defineProps({
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    mode: { type: String, required: false, default: 'create' },
  })

  const renderers = Object.freeze([
    ...vanillaRenderers,
    // custom renderers
    myStringControlRendererEntry,
  ])

  provide('styles', mergeStyles(defaultStyles, {
    control: {
      root: 'flex flex-col gap-y-1',
      label: 'block text-sm font-medium text-gray-700',
      input: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm',
      error: 'text-xs text-red-500 ml-1',
      asterisk: 'text-red-500',
    },
    container: {
      label: 'flex items-center gap-x-2 ml-1',
    },
    tooltip: {
      icon: 'mdi mdi-information-slab-circle-outline',
    },
    group: {
      root: 'flex flex-col gap-y-4',
      label: 'mb-2',
    },
  }))

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

<style lang="postcss" scoped>
  .cancel-button {
    @apply block rounded-md bg-sinai-red px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-sinai-red focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sinai-red
  }

  .save-button {
    @apply block rounded-md bg-gray-500 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-500
  }
</style>
