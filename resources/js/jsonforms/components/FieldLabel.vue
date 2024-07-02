<template>
  <div class="flex items-center gap-x-4">
    <label
      :for="control.id + '-input'"
      class="block font-medium text-sm text-gray-700 ml-1"
      :class="[appliedOptions.requiredForPublishing ? 'required-for-publishing' : control.required ? 'required' : '']">
      {{ control.label }}
    </label>

    <div
      v-if="control.description && persistentHint()"
      class="text-xs">
      {{ control.description }}
    </div>
  </div>
</template>

<script setup>
  defineProps({
    control: Object,
    appliedOptions: Object,
    persistentHint: Function,
  })
</script>
