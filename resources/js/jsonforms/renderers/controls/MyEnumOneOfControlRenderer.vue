<template>
  <control-wrapper
    v-bind="controlWrapper"
    :styles="styles"
    :is-focused="isFocused"
    :applied-options="appliedOptions">
    <select
      :id="control.id + '-input'"
      :class="styles.control.select"
      :value="control.data"
      :disabled="!control.enabled"
      :autofocus="appliedOptions.focus"
      @change="onChange"
      @focus="isFocused = true"
      @blur="isFocused = false">
      <option key="empty" value="" :class="styles.control.option" />
      <option
        v-for="optionElement in control.options"
        :key="optionElement.value"
        :value="optionElement.value"
        :label="optionElement.label"
        :class="styles.control.option"
      />
    </select>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsOneOfEnumControl } from '@jsonforms/vue'
  import { default as ControlWrapper } from './MyControlWrapper.vue'
  import { useVanillaControl } from '@jsonforms/vue-vanilla/src/util'

  const controlRenderer = defineComponent({
    name: 'MyEnumOneOfControlRenderer',

    components: {
      ControlWrapper,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      return useVanillaControl(useJsonFormsOneOfEnumControl(props), (target) =>
        target.selectedIndex === 0 ? undefined : target.value
      )
    },
  })

  export default controlRenderer
</script>
