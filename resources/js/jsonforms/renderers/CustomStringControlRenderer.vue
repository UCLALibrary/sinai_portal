<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :isFocused="isFocused"
    :appliedOptions="appliedOptions">
    <div class="flex flex-col space-y-2">
      <FieldLabel
        :control="control"
        :appliedOptions="appliedOptions"
        :persistentHint="persistentHint"
      />

      <input
        type="text"
        :id="control.id + '-input'"
        :name="control.id + '-input'"
        :placeholder="appliedOptions.placeholder"
        :disabled="!control.enabled"
        v-model="control.data"
        @update:model-value="onChange"
        class="w-full border rounded">

      <FieldErrors :control="control" />
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, useJsonFormsControl, RendererProps } from '@jsonforms/vue'
  import { useVuetifyControl } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'
  import FieldLabel from '@/jsonforms/components/FieldLabel.vue'
  import FieldErrors from '@/jsonforms/components/FieldErrors.vue'

  const controlRenderer = defineComponent({
    name: 'custom-string-control-renderer',

    components: {
      ControlWrapper,
      FieldLabel,
      FieldErrors,
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
