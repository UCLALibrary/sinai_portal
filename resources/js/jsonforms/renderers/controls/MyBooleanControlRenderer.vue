<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :is-focused="isFocused"
    :applied-options="appliedOptions">
    <div :class="styles.container.checkbox">
      <input
        :id="control.id + '-input'"
        type="checkbox"
        :class="styles.control.checkbox"
        :checked="!!control.data"
        :disabled="!control.enabled"
        :autofocus="appliedOptions.focus"
        :placeholder="appliedOptions.placeholder"
        @change="onChange"
        @focus="isFocused = true"
        @blur="isFocused = false"
      />

      <label :for="control.id + '-input'" :class="styles.checkbox.label">
        {{ appliedOptions.label }}
      </label>
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsControl } from '@jsonforms/vue'
  import { default as ControlWrapper } from './MyControlWrapper.vue'
  import { useVanillaControl } from '@jsonforms/vue-vanilla/src/util'
  import FieldLabel from '@/jsonforms/components/FieldLabel.vue'

  const controlRenderer = defineComponent({
    name: 'MyBooleanControlRenderer',

    components: {
      ControlWrapper,
      FieldLabel,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      return useVanillaControl(
        useJsonFormsControl(props),
        (target) => target.checked
      )
    },
  })

  export default controlRenderer
</script>
