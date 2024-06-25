/**
 * When building for production with Vite, custom renderers will not work correctly if their entries 
 * are defined within the Single File Component - ie within the custom renderer itself.
 *
 * To use custom renderers with Vite in production mode, define the entry for any custom renderer 
 * outside of the SFC that defines the renderer itself. For example, within the file that imports 
 * and registers the renderer. Example repo: https://github.com/yaffol/json-forms-vuetify-vite-seed
 * 
 * See https://github.com/eclipsesource/jsonforms-vuetify-renderers
*/

import {
  JsonFormsRendererRegistryEntry,
  rankWith,
  Tester,
} from '@jsonforms/core'

export default function buildRendererRegistryEntry(renderer: any, tester: Tester, rank: number = 4) {
  const entry: JsonFormsRendererRegistryEntry = {
    renderer: renderer,
    tester: rankWith(rank, tester)
  }
  return entry
}
