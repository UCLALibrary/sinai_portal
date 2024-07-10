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
  import { defaultStyles, mergeStyles, vanillaRenderers } from '@jsonforms/vue-vanilla'
  import { customRenderers } from '@/jsonforms/renderers/useCustomRenderers.js'

  const emit = defineEmits(['on-save', 'on-cancel']);

  const props = defineProps({
    schema: { type: Object, required: true },
    uischema: { type: Object, required: true },
    data: { type: Object, required: false, default: () => {} },
    mode: { type: String, required: false, default: 'create' },
  })

  const renderers = Object.freeze([
    ...vanillaRenderers,
    ...customRenderers,
  ])

  provide('styles', mergeStyles(defaultStyles, {
    // extend defaults
    control: {
      root: 'flex flex-col gap-y-1',
      label: 'block text-sm font-medium text-gray-700',
      input: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm',
      checkbox: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm cursor-pointer',
      select: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm',
      error: 'text-xs text-red-500 ml-1',
      asterisk: 'text-red-500',
    },
    verticalLayout: {
      root: 'flex flex-col gap-y-2',
    },
    group: {
      root: 'flex flex-col gap-y-4 mb-8',
      label: 'w-full text-xl font-semibold bg-transparent border-b pb-2 mb-4',
    },
    arrayList: {
      root: 'border border-gray-300 divide-y divide-gray-300',
      legend: 'flex flex-row-reverse w-full justify-between mb-2',
      addButton: 'mdi mdi-plus-circle-outline focus:outline-none focus:ring-2 focus:ring-sinai-red focus:border-sinai-red cursor-pointer mr-1',
      label: 'block text-sm font-medium text-gray-700',
      itemWrapper: '',
      noData: 'px-3 py-2 text-sm bg-gray-200',
      itemToolbar: '!m-0 p-2 border-b bg-gray-200',
      itemLabel: '!px-1 bg-transparent text-sm',
      itemContent: '!px-2 py-4',
      itemExpanded: '',
      itemMoveUp: 'mdi mdi-arrow-up-thin',
      itemMoveDown: 'mdi mdi-arrow-down-thin',
      itemDelete: 'mdi mdi-delete',
    },
    checkbox: {
      label: 'block text-sm font-medium text-gray-700 cursor-pointer'
    },
    // custom classes
    container: {
      label: 'flex items-center gap-x-2',
      checkbox: 'relative flex items-center gap-x-1 my-1',
      checkboxList: 'gap-y-2 my-2',
      arrayList: 'flex items-center gap-x-2',
    },
    tooltip: {
      icon: 'mdi mdi-information-slab-circle-outline cursor-pointer',
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
