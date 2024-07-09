<template>
  <div :class="styles.container.arrayList">
    <label :class="[styles.arrayList.label, required ? styles.control.required : '']">
      {{ label }}
      <span v-if="showAsterisk" :class="styles.control.asterisk">*</span>
      <span v-if="showAsteriskForPublishing" :class="styles.control.asterisk">*</span>
    </label>

    <Tooltip v-if="showDescription" :triggers="['hover', 'click']">
      <span :class="styles.tooltip.icon"></span>
      <template #popper>
        <span class="text-xs">
          {{ description }}
        </span>
      </template>
    </Tooltip>
  </div>
</template>

<script setup>
  import { computed } from 'vue'
  import { isDescriptionHidden } from '@jsonforms/core'
  import { Tooltip } from 'floating-vue'

  const props = defineProps({
    id: { type: String },
    label: { type: String },
    description: { type: String },
    visible: { type: Boolean },
    required: { type: Boolean },
    isFocused: { type: Boolean },
    appliedOptions: { type: Object },
    styles: { type: Object },
  })

  const showDescription = computed(() => {
    return !isDescriptionHidden(
      props.visible,
      props.description,
      props.isFocused,
      !!props.appliedOptions?.showUnfocusedDescription
    )
  })

  const showAsterisk = computed(() => {
    return props.required && !props.appliedOptions?.hideRequiredAsterisk
  })

  const showAsteriskForPublishing = computed(() => {
    return props.appliedOptions?.showRequiredAsteriskForPublishing ?? false
  })
</script>
