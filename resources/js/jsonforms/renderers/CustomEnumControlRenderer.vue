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

      <div
        v-if="control.description && persistentHint()"
        class="text-xs ml-2">
        {{ control.description }}
      </div>
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsOneOfEnumControl } from '@jsonforms/vue'
  import { useVuetifyControl, useTranslator } from '@jsonforms/vue-vuetify/src/util'
  import { default as ControlWrapper } from '@jsonforms/vue-vuetify/src/controls/ControlWrapper.vue'

  const controlRenderer = defineComponent({
    name: 'custom-enum-control-renderer',

    components: {
      ControlWrapper,
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
