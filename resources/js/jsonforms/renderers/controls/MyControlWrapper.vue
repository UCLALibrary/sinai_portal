<template>
  <div v-if="visible" :id="id" :class="styles.control.root">
    <div :class="styles.container.label">
      <FieldLabel
        :id="id + '-input'"
        :label="label"
        :description="description"
        :visible="visible"
        :required="required"
        :applied-options="appliedOptions"
        :styles="styles"
      />
      <div class="flex flex-col flex-1">
        <div :class="styles.control.wrapper">
          <slot></slot>
        </div>
        <FieldErrors
          :errors="errors"
          :styles="styles.control.error"
        />
      </div>

      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent, PropType } from 'vue'
  import { Styles } from '@jsonforms/vue-vanilla/src/styles'
  import { Options } from '@jsonforms/vue-vanilla/src/util'
  import FieldLabel from '@/jsonforms/components/FieldLabel.vue'
  import FieldErrors from '@/jsonforms/components/FieldErrors.vue'

  export default defineComponent({
    name: 'MyControlWrapper',

    components: {
      FieldLabel,
      FieldErrors,
    },

    props: {
      id: { type: String, required: true },
      description: { type: String, required: false as const, default: undefined },
      errors: { type: String, required: false as const, default: undefined },
      label: { type: String, required: false as const, default: undefined },
      appliedOptions: { type: Object as PropType<Options>, required: false as const, default: undefined },
      visible: { type: Boolean, required: false as const, default: true },
      required: { type: Boolean, required: false as const, default: false },
      isFocused: { type: Boolean, required: false as const, default: false },
      styles: { type: Object as PropType<Styles>, required: true },
    },
  })
</script>

<style>
  .array-list-item {
    label { 
      @apply min-w-40
    }
  }
</style>