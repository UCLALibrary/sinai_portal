<template>
  <div class="flex gap-x-2">
    <label
      :for="control.id + '-input'"
      class="block font-medium text-sm text-gray-700 ml-1"
      :class="[appliedOptions.requiredForPublishing ? 'required-for-publishing' : control.required ? 'required' : '']">
      {{ control.label }}
    </label>

    <Tooltip v-if="control.description && persistentHint()">
      <span class="mdi mdi-information-slab-circle-outline"></span>
      <template #popper>
        <span class="text-xs">
          {{ control.description }}
        </span>
      </template>
    </Tooltip>
  </div>
</template>

<script setup>
  import { Tooltip } from 'floating-vue'

  defineProps({
    control: Object,
    appliedOptions: Object,
    persistentHint: Function,
  })
</script>
