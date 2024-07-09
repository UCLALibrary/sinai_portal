<template>
  <div :class="styles.arrayList.item">
    <div :class="toolbarClasses" @click="expandClicked">
      <div :class="styles.arrayList.itemLabel">
        {{ label }}
      </div>

      <button
        :disabled="!moveUpEnabled"
        :class="[styles.arrayList.itemMoveUp, !moveUpEnabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer']"
        type="button"
        @click="moveUpClicked">
      </button>

      <button
        :disabled="!moveDownEnabled"
        :class="[styles.arrayList.itemMoveDown, !moveDownEnabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer']"
        type="button"
        @click="moveDownClicked">
      </button>

      <button
        :disabled="!deleteEnabled"
        :class="[styles.arrayList.itemDelete, !deleteEnabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer']"
        type="button"
        @click="deleteClicked">
      </button>
    </div>

    <div :class="contentClasses">
      <slot></slot>
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent, PropType } from 'vue'
  import { classes, Styles } from '@jsonforms/vue-vanilla/src/styles'

  const listItem = defineComponent({
    name: 'MyArrayListElement',

    props: {
      initiallyExpanded: { type: Boolean, required: false, default: false },
      label: { type: String, required: false, default: '' },
      moveUpEnabled: { type: Boolean, required: false, default: true },
      moveDownEnabled: { type: Boolean, required: false, default: true },
      moveUp: { type: Function, required: false, default: undefined },
      moveDown: { type: Function, required: false, default: undefined },
      deleteEnabled: { type: Boolean, required: false, default: true },
      delete: { type: Function, required: false, default: undefined },
      styles: { type: Object as PropType<Styles>, required: true },
    },

    data() {
      return {
        expanded: this.initiallyExpanded,
      }
    },

    computed: {
      contentClasses(): string {
        return classes`${this.styles.arrayList.itemContent} ${
          this.expanded && this.styles.arrayList.itemExpanded
        }`
      },

      toolbarClasses(): string {
        return classes`${this.styles.arrayList.itemToolbar} ${
          this.expanded && this.styles.arrayList.itemExpanded
        }`
      },
    },

    methods: {
      expandClicked(): void {
        this.expanded = !this.expanded
      },

      moveUpClicked(event: Event): void {
        event.stopPropagation()
        this.moveUp?.()
      },

      moveDownClicked(event: Event): void {
        event.stopPropagation()
        this.moveDown?.()
      },

      deleteClicked(event: Event): void {
        event.stopPropagation()
        this.delete?.()
      },
    },
  })

  export default listItem
</script>
