<template>
  <control-wrapper
    :id="control.id"
    :label="control.label"
    :styles="styles"
    :applied-options="appliedOptions"
    class="gap-y-2 my-2">
    <div :class="styles.container.checkboxList">
      <div v-for="(checkElement, index) in control.options" :key="index" :class="styles.container.checkbox">
        <input
          :id="control.id + `-input-${index}`"
          type="checkbox"
          :class="styles.control.checkbox"
          :value="checkElement.value"
          :checked="dataHasEnum(checkElement.value)"
          :disabled="!control.enabled"
          :placeholder="appliedOptions?.placeholder"
          @change="(event) => toggle(checkElement.value, event.target.checked)"
        />

        <label :for="control.id + `-input-${index}`" :class="styles.checkbox.label">
          {{ checkElement.label }}
        </label>
      </div>
    </div>
  </control-wrapper>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { ControlElement } from '@jsonforms/core'
  import { rendererProps, RendererProps, useJsonFormsMultiEnumControl } from '@jsonforms/vue'
  import { default as ControlWrapper } from '../controls/MyControlWrapper.vue'
  import { useVanillaArrayControl } from '@jsonforms/vue-vanilla/src/util'

  const controlRenderer = defineComponent({
    name: 'MyEnumArrayRenderer',

    components: {
      ControlWrapper,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      const control = useJsonFormsMultiEnumControl(props)
      
      return useVanillaArrayControl(control)
    },

    methods: {
      dataHasEnum(value: any): boolean {
        return !!this.control.data?.includes(value)
      },

      toggle(value: any, checked: boolean): void {
        if (checked) {
          this.addItem(this.control.path, value)
          
        } else {
          this.removeItem?.(this.control.path, value)
        }
      },
    },
  })

  export default controlRenderer
</script>