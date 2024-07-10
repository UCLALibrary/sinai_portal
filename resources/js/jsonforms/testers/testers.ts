import {
  and,
  JsonSchema,
  schemaMatches,
  schemaSubPathMatches,
  uiTypeIs,
  hasType
} from '@jsonforms/core'

const hasOneOfItems = (schema: JsonSchema): boolean =>
  schema.oneOf !== undefined &&
  schema.oneOf.length > 0 &&
  (schema.oneOf as JsonSchema[]).every((entry: JsonSchema) => {
    return entry.const !== undefined
  })

const hasEnumItems = (schema: JsonSchema): boolean =>
  schema.type === 'string' && schema.enum !== undefined

/**
 * Tests whether the given schema is an array of enum items.
 * @type {Tester}
 */
export const isMultiEnumControl = and(
  uiTypeIs('Control'),
  and(
    schemaMatches(
      (schema) =>
        hasType(schema, 'array') &&
        !Array.isArray(schema.items) &&
        schema.uniqueItems === true
    ),
    schemaSubPathMatches('items', (schema) => {
      return hasOneOfItems(schema) || hasEnumItems(schema)
    })
  )
)
