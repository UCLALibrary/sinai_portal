import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'
import {
  isStringControl,
  isOneOfEnumControl,
} from '@jsonforms/core'

// text fields
import myStringControlRenderer from '@/jsonforms/renderers/controls/MyStringControlRenderer.vue'
export const myStringControlRendererEntry = buildRendererRegistryEntry(myStringControlRenderer, isStringControl)

// select menus (used to choose enum options from a list)
import myEnumOneOfControlRenderer from '@/jsonforms/renderers/controls/MyEnumOneOfControlRenderer.vue'
export const myEnumOneOfControlRendererEntry = buildRendererRegistryEntry(myEnumOneOfControlRenderer, isOneOfEnumControl, 6)
