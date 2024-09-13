<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :is-focused="isFocused"
    :applied-options="appliedOptions">
    <input
      :id="control.id + '-input'"
      :type="appliedOptions.format || 'number'"
      :class="styles.control.input"
      :value="control.data"
      :disabled="!control.enabled"
      :autofocus="appliedOptions.focus"
      :placeholder="appliedOptions.placeholder"
      @change="onChange"
      @focus="isFocused = true"
      @blur="isFocused = false"
    />
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsControl } from '@jsonforms/vue'
  import { default as ControlWrapper } from './MyInlineControlWrapper.vue'
  import { useVanillaControl } from '@jsonforms/vue-vanilla/src/util'

  const controlRenderer = defineComponent({
    name: 'MyIntegerControlRenderer',

    components: {
      ControlWrapper,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      return useVanillaControl(
        useJsonFormsControl(props),
        (target) => target.value || undefined
      )
    },
  })

  export default controlRenderer
</script>
