<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <div class="flex flex-col space-y-2">
      <label
        :for="control.id + '-input'"
        class="block font-medium text-sm text-gray-700 ml-1"
        :class="[appliedOptions.requiredForPublishing ? 'required-for-publishing' : control.required ? 'required' : '']">
        {{ control.label }}
      </label>

      <input
        type="text"
        :id="control.id + '-input'"
        :name="control.id + '-input'"
        :placeholder="appliedOptions.placeholder"
        :disabled="!control.enabled"
        v-model="control.data"
        @update:model-value="onChange"
        class="w-full border rounded">

      <div
        v-if="control.description && persistentHint()"
        class="text-xs ml-1">
        {{ control.description }}
      </div>

      <div
        v-if="control.required || control.errors"
        class="text-xs text-red-500 ml-2">
        {{ control.errors }}
      </div>
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, useJsonFormsControl, RendererProps } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'

  const controlRenderer = defineComponent({
    name: 'custom-string-control-renderer',

    components: {
      ControlWrapper,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      return useVuetifyControl(
        useJsonFormsControl(props),
        (value) => value || undefined,
        300
      )
    },
  })

  export default controlRenderer
</script>
