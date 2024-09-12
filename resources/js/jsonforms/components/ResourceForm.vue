<template>
  <div class="flex flex-col">
    <JsonForms
      :data="jsonData"
      :renderers="renderers"
      :schema="schema"
      :uischema="uischema"
      @change="onChange"
    />

    <div class="flex justify-start space-x-4 mt-8 pt-4 border-t">
      <template v-if="mode !== 'show'">
        <button
          type="button"
          @click="onCancel"
          class="cancel-button">
          <i class="mdi mdi-cancel"></i> Cancel
        </button>
      </template>

      <template v-if="mode === 'create' || mode === 'update'">
        <button
          type="button"
          @click="onSave(false)"
          class="save-button"
          :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
          :disabled="!isValid">
          <i class="mdi mdi-content-save"></i> Save
        </button>
      </template>
      <template v-else-if="mode === 'edit'">
        <button
          type="button"
          @click="onSave(true)"
          class="save-button"
          :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
          :disabled="!isValid">
          <i class="mdi mdi-content-save-edit"></i> Save &amp; Continue
        </button>

        <button
          type="button"
          @click="onSave(false)"
          class="save-button"
          :class="{ 'cursor-not-allowed pointer-events-auto opacity-50': !isValid }"
          :disabled="!isValid">
          <i class="mdi mdi-content-save"></i> Save &amp; Finish
        </button>
      </template>
      <template v-else-if="mode === 'show'">
        <button
          type="button"
          @click="onCancel"
          class="cancel-button">
          <i class="mdi mdi-close"></i> Close
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
  import { ref, provide } from 'vue'
  import { JsonForms } from '@jsonforms/vue'
  import { defaultStyles, mergeStyles, vanillaRenderers } from '@jsonforms/vue-vanilla'
  import { customRenderers } from '@/jsonforms/renderers/useCustomRenderers.js'
  import customStyles from '@/jsonforms/styles/customStyles.js'

  const emit = defineEmits(['on-save', 'on-cancel']);

  const props = defineProps({
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    mode: { type: String, required: true },
  })

  const renderers = Object.freeze([
    ...vanillaRenderers,
    ...customRenderers,
  ])

  provide('styles', mergeStyles(defaultStyles, customStyles))

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
