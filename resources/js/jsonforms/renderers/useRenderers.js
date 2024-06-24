import { scopeEndIs } from '@jsonforms/core'
import buildRendererRegistryEntry from '@/jsonforms/testers/registry.ts'

import manuscriptSelectionRenderer from '@/jsonforms/renderers/ManuscriptSelectionRenderer.vue'
export const manuscriptSelectionRendererEntry = buildRendererRegistryEntry(manuscriptSelectionRenderer, scopeEndIs('ms_objs'))

import partSelectionRenderer from '@/jsonforms/renderers/PartSelectionRenderer.vue'
export const partSelectionRendererEntry = buildRendererRegistryEntry(partSelectionRenderer, scopeEndIs('parts'))

import dateSelectionRenderer from '@/jsonforms/renderers/DateSelectionRenderer.vue'
export const dateSelectionRendererEntry = buildRendererRegistryEntry(dateSelectionRenderer, scopeEndIs('assoc_date'), 5)
