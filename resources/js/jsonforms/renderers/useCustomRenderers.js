import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'
import {
  uiTypeIs,
  isStringControl,
  isBooleanControl,
  isEnumControl,
  isOneOfEnumControl,
  isArrayObjectControl,
} from '@jsonforms/core'
import {
  isMultiEnumControl
} from '@/jsonforms/testers/testers.ts'

import myHorizontalRuleRenderer from '@/jsonforms/renderers/layout/MyHorizontalRuleRenderer.vue'
import myStringControlRenderer from '@/jsonforms/renderers/controls/MyStringControlRenderer.vue'
import myBooleanControlRenderer from '@/jsonforms/renderers/controls/MyBooleanControlRenderer.vue'
import myEnumControlRenderer from '@/jsonforms/renderers/controls/MyEnumControlRenderer.vue'
import myEnumOneOfControlRenderer from '@/jsonforms/renderers/controls/MyEnumOneOfControlRenderer.vue'
import myArrayListRenderer from '@/jsonforms/renderers/array/MyArrayListRenderer.vue'
import myEnumArrayRenderer from '@/jsonforms/renderers/complex/MyEnumArrayRenderer.vue'

// layout renderer: horizontal rule
export const myHorizontalRuleRendererEntry = buildRendererRegistryEntry(myHorizontalRuleRenderer, uiTypeIs('HorizontalRule'))

// control renderer: text fields
export const myStringControlRendererEntry = buildRendererRegistryEntry(myStringControlRenderer, isStringControl)

// control renderer: checkbox
export const myBooleanControlRendererEntry = buildRendererRegistryEntry(myBooleanControlRenderer, isBooleanControl)

// control renderer: select menu (used to choose one enum string option from a list)
export const myEnumControlRendererEntry = buildRendererRegistryEntry(myEnumControlRenderer, isEnumControl, 5)

// control renderer: select menu (used to choose one enum object option from a list)
export const myEnumOneOfControlRendererEntry = buildRendererRegistryEntry(myEnumOneOfControlRenderer, isOneOfEnumControl, 6)

// array renderer: array lists (used to display a list of objects)
export const myArrayListRendererEntry = buildRendererRegistryEntry(myArrayListRenderer, isArrayObjectControl)

// complex renderer: checkbox list (used to choose multiple enum options from a list)
export const myEnumArrayRendererEntry = buildRendererRegistryEntry(myEnumArrayRenderer, isMultiEnumControl, 6)

export const customRenderers = [
  myHorizontalRuleRendererEntry,
  myStringControlRendererEntry,
  myBooleanControlRendererEntry,
  myEnumControlRendererEntry,
  myEnumOneOfControlRendererEntry,
  myArrayListRendererEntry,
  myEnumArrayRendererEntry
]
