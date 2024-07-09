import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'
import {
  uiTypeIs,
  isStringControl,
  isEnumControl,
  isOneOfEnumControl,
} from '@jsonforms/core'

// horizontal rule
import myHorizontalRuleRenderer from '@/jsonforms/renderers/layout/MyHorizontalRuleRenderer.vue'
export const myHorizontalRuleRendererEntry = buildRendererRegistryEntry(myHorizontalRuleRenderer, uiTypeIs('HorizontalRule'))

// text fields
import myStringControlRenderer from '@/jsonforms/renderers/controls/MyStringControlRenderer.vue'
export const myStringControlRendererEntry = buildRendererRegistryEntry(myStringControlRenderer, isStringControl)

// select menus (used to choose enum options from a list)
import myEnumControlRenderer from '@/jsonforms/renderers/controls/MyEnumControlRenderer.vue'
export const myEnumControlRendererEntry = buildRendererRegistryEntry(myEnumControlRenderer, isEnumControl, 5)

import myEnumOneOfControlRenderer from '@/jsonforms/renderers/controls/MyEnumOneOfControlRenderer.vue'
export const myEnumOneOfControlRendererEntry = buildRendererRegistryEntry(myEnumOneOfControlRenderer, isOneOfEnumControl, 6)
