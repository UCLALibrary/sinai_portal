<template>
  <legend :class="styles.arrayList.legend">
    <button
      :class="styles.arrayList.addButton"
      type="button"
      :disabled="!control.enabled || (appliedOptions.restrict && maxItemsReached)"
      @click="addButtonClick">
    </button>

    <ArrayListLabel
      :id="control.id"
      :label="control.label"
      :description="control.description"
      :visible="control.visible"
      :required="control.required"
      :is-focused="true"
      :applied-options="appliedOptions"
      :styles="styles"
    />
  </legend>

  <fieldset v-if="control.visible" :class="styles.arrayList.root">
    <div
      v-for="(element, index) in control.data"
      :key="`${control.path}-${index}`"
      :class="styles.arrayList.itemWrapper">
      <MyArrayListElement
        :initially-expanded="true"
        :move-up="moveUp(control.path, index)"
        :move-up-enabled="control.enabled && index > 0"
        :move-down="moveDown(control.path, index)"
        :move-down-enabled="control.enabled && index < control.data.length - 1"
        :delete-enabled="control.enabled && !minItemsReached"
        :delete="removeItems(control.path, [index])"
        :label="childLabelForIndex(index) !== '' ? childLabelForIndex(index) : `${control.label} ${index + 1}`"
        :styles="styles">
        <dispatch-renderer
          :schema="control.schema"
          :uischema="childUiSchema"
          :path="composePaths(control.path, `${index}`)"
          :enabled="control.enabled"
          :renderers="control.renderers"
          :cells="control.cells"
        />
      </MyArrayListElement>
    </div>

    <div v-if="noData" :class="styles.arrayList.noData">
      {{ control.translations.noDataMessage }}
    </div>
  </fieldset>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { composePaths, createDefaultValue, ControlElement, Resolve, JsonSchema } from '@jsonforms/core'
  import { DispatchRenderer, rendererProps, RendererProps, useJsonFormsArrayControl } from '@jsonforms/vue'
  import { useVanillaArrayControl } from '@jsonforms/vue-vanilla/src/util'
  import MyArrayListElement from './MyArrayListElement.vue'
  import ArrayListLabel from '@/jsonforms/components/ArrayListLabel.vue'

  const controlRenderer = defineComponent({
    name: 'MyArrayListRenderer',

    components: {
      MyArrayListElement,
      ArrayListLabel,
      DispatchRenderer,
    },

    props: {
      ...rendererProps<ControlElement>(),
    },

    setup(props: RendererProps<ControlElement>) {
      return useVanillaArrayControl(useJsonFormsArrayControl(props))
    },

    computed: {
      noData(): boolean {
        return !this.control.data || this.control.data.length === 0
      },

      arraySchema(): JsonSchema | undefined {
        return Resolve.schema(
          this.schema,
          this.control.uischema.scope,
          this.control.rootSchema
        )
      },

      maxItemsReached(): boolean | undefined {
        return (
          this.arraySchema !== undefined &&
          this.arraySchema.maxItems !== undefined &&
          this.control.data !== undefined &&
          this.control.data.length >= this.arraySchema.maxItems
        )
      },

      minItemsReached(): boolean | undefined {
        return (
          this.arraySchema !== undefined &&
          this.arraySchema.minItems !== undefined &&
          this.control.data !== undefined &&
          this.control.data.length <= this.arraySchema.minItems
        )
      },
    },
    methods: {
      composePaths,
      createDefaultValue,
      addButtonClick() {
        this.addItem(
          this.control.path,
          createDefaultValue(this.control.schema, this.control.rootSchema)
        )()
      },
    },
  })

  export default controlRenderer
</script>
