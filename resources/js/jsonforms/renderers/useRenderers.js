import { schemaTypeIs, isOneOfEnumControl, scopeEndIs } from '@jsonforms/core'
import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'

// text field renderer
import customStringControlRenderer from '@/jsonforms/renderers/CustomStringControlRenderer.vue'
export const customStringControlRendererEntry = buildRendererRegistryEntry(customStringControlRenderer, schemaTypeIs('string'))

// select menu renderer (used to choose enum values from a list)
import customEnumControlRenderer from '@/jsonforms/renderers/CustomEnumControlRenderer.vue'
export const customEnumControlRendererEntry = buildRendererRegistryEntry(customEnumControlRenderer, isOneOfEnumControl, 11)

// manuscript selection renderer (used to attach manuscript records to parts)
import manuscriptSelectionRenderer from '@/jsonforms/renderers/ManuscriptSelectionRenderer.vue'
export const manuscriptSelectionRendererEntry = buildRendererRegistryEntry(manuscriptSelectionRenderer, scopeEndIs('ms_objs'))

// part selection renderer (used to attach part records to manuscripts)
import partSelectionRenderer from '@/jsonforms/renderers/PartSelectionRenderer.vue'
export const partSelectionRendererEntry = buildRendererRegistryEntry(partSelectionRenderer, scopeEndIs('parts'))

// date selection renderer (used to attach date records to manuscripts and parts)
import dateSelectionRenderer from '@/jsonforms/renderers/DateSelectionRenderer.vue'
export const dateSelectionRendererEntry = buildRendererRegistryEntry(dateSelectionRenderer, scopeEndIs('assoc_date'), 5)
