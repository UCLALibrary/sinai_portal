<template>
  <div v-if="visible" :id="id" :class="styles.control.root">
    <div :class="styles.label.wrapper">
      <label
        :for="id"
        :class="[styles.control.label, required ? styles.control.required : '']">
        {{ label }}
        <div v-if="showAsterisk || showAsteriskForPublishing">
          <span v-if="showAsterisk" :class="styles.control.asterisk">*</span>
          <span v-if="showAsteriskForPublishing" :class="styles.control.asterisk">*</span>
          <span v-else> &nbsp;</span>
        </div>
      </label>

      <div class="flex flex-col flex-1">
        <div :class="styles.control.wrapper">
          <slot></slot>

          <Tooltip v-if="showDescription" :triggers="['hover', 'click']">
            <span :class="styles.tooltip.icon"></span>
            <template #popper>
              <span class="text-xs">
                {{ description }}
              </span>
            </template>
          </Tooltip>
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
  import { defineComponent, PropType, computed } from 'vue'
  import { isDescriptionHidden } from '@jsonforms/core'
  import { Styles } from '@jsonforms/vue-vanilla/src/styles'
  import { Options } from '@jsonforms/vue-vanilla/src/util'
  import FieldLabel from '@/jsonforms/components/FieldLabel.vue'
  import FieldErrors from '@/jsonforms/components/FieldErrors.vue'
  import { Tooltip } from 'floating-vue'

  export default defineComponent({
    name: 'MyInlineControlWrapper',

    components: {
      FieldLabel,
      FieldErrors,
      Tooltip,
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

    setup(props) {
      const showDescription = computed(() => {
        return !isDescriptionHidden(
          props.visible,
          props.description,
          false,
          !!props.appliedOptions?.showUnfocusedDescription
        )
      })

      const showAsterisk = computed(() => {
        return props.required && !props.appliedOptions?.hideRequiredAsterisk
      })

      const showAsteriskForPublishing = computed(() => {
        return props.appliedOptions?.showRequiredAsteriskForPublishing ?? false
      })

      return {
        showDescription,
        showAsterisk,
        showAsteriskForPublishing,
      }
    }
  })
</script>
