import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'
import {
  isStringControl,
} from '@jsonforms/core'

// text fields
import myStringControlRenderer from '@/jsonforms/renderers/controls/MyStringControlRenderer.vue'
export const myStringControlRendererEntry = buildRendererRegistryEntry(myStringControlRenderer, isStringControl)
