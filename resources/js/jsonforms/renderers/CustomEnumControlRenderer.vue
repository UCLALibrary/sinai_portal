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

      <select
        :id="control.id + '-input'"
        :name="control.id + '-input'"
        class="w-full border rounded"
        v-model="control.data"
        :disabled="!control.enabled"
        @update:model-value="onChange">
        <option
          v-for="(option, index) in control.options"
          :key="index"
          :value="option.value">
          {{ option.label }}
        </option>
      </select>

      <FieldErrors :control="control" />
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsOneOfEnumControl } from '@jsonforms/vue'
  import { useVuetifyControl, useTranslator } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'
  import FieldLabel from '@/jsonforms/components/FieldLabel.vue'
  import FieldErrors from '@/jsonforms/components/FieldErrors.vue'

  const controlRenderer = defineComponent({
    name: 'custom-enum-control-renderer',

    components: {
      ControlWrapper,
      FieldLabel,
      FieldErrors,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      const t = useTranslator()

      const control = useVuetifyControl(
        useJsonFormsOneOfEnumControl(props),
        (value) => value || undefined,
        300
      )

      return {
        ...control,
        t
      }
    },
  })

  export default controlRenderer
</script>
