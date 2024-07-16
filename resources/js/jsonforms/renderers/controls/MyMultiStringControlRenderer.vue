<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :is-focused="isFocused"
    :applied-options="appliedOptions">
    <textarea
      :id="control.id + '-input'"
      :class="styles.control.textarea"
      :value="control.data"
      :disabled="!control.enabled"
      :autofocus="appliedOptions.focus"
      :placeholder="appliedOptions.placeholder"
      :rows="appliedOptions.rows"
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
  import { default as ControlWrapper } from './MyControlWrapper.vue'
  import { useVanillaControl } from '@jsonforms/vue-vanilla/src/util'

  const controlRenderer = defineComponent({
    name: 'MyMultiStringControlRenderer',

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
