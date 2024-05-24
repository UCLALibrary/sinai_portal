<template>
  <AppLayout title="Add Date">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Dates > Add Date
            </h1>

            <JsonForms
              :data="data"
              :renderers="renderers"
              :schema="schema"
              :uischema="uischema"
              @change="onChange"
            />

            <button
              type="button"
              class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
              @click="onSave">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
  import { ref, defineComponent } from 'vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { JsonForms } from '@jsonforms/vue'
  import {
    defaultStyles,
    mergeStyles,
    vuetifyRenderers,
  } from '@jsonforms/vue-vuetify'

  const renderers = [
    ...vuetifyRenderers,
    // custom renderers
  ]

  const uischema = {
    type: 'VerticalLayout',
    elements: [
      {
        type: 'Control',
        scope: '#/properties/type',
      },
      {
        type: 'Control',
        scope: '#/properties/as_written',
      },
      {
        type: 'HorizontalLayout',
        elements: [
          {
            type: 'Control',
            scope: '#/properties/not_before',
          },
          {
            type: 'Control',
            scope: '#/properties/not_after',
          },
        ]
      },
      {
        type: 'Control',
        scope: '#/properties/note',
      },
    ]
  }

  const schema = ref(
    {
      // "$schema": "https://json-schema.org/draft/2020-12/schema",
      "$id": "https://raw.githubusercontent.com/UCLALibrary/sinai_metadata/master/data-model/jsonschemas/assoc_date.json",
      "title": "Associated Date",
      "description": "A date associated with an object, either attested or inferred through indirect means (paleography, etc.)",
      "type": "object",
      "properties": {
        "type": {
          "type": "string",
          "enum": [
            "creation",
            "binding",
            "purchase",
            "other"
          ],
          "$comment": "TBD full list of types"
        },
        "not_before": {
          "type": "string",
          "format": "date",
          "minLength": 3
        },
        "not_after": {
          "type": [
            "string",
            "null"
          ],
          "format": "date",
          "minLength": 3
        },
        "as_written": {
          "type": [
            "string",
            "null"
          ],
          "minLength": 1
        },
        "note": {
          "type": [
            "string",
            "null"
          ],
        }
      },
      "required": [
        "type",
        "not_before",
        "not_after",
        "as_written",
      ],
      "unevaluatedProperties": false
    }
  )

  const data = {}

  // mergeStyles combines all classes from both styles definitions
  const myStyles = mergeStyles(defaultStyles, {
    control: {
      root: 'my-control'
    }
  })

  export default defineComponent({
    name: 'CreateEdit',

    components: {
      AppLayout,
      JsonForms
    },

    props: {
      metadata: { type: Object, required: false, default: null },
    },

    data() {
      return {
        renderers: Object.freeze(renderers),
        data,
        schema,
        uischema,
      }
    },

    mounted() {
      // initialize the form with the supplied metadata
      this.data = this.metadata ?? {}
    },

    // computed: {
    //   saveEndpoint() {
    //     return this.metadata ? route('dates.update') : route('dates.store')
    //   }
    // },

    methods: {
      onChange(event) {
        this.data = event.data
      },

      onSave() {
        console.log('saving date')
        // axios.post(saveEndpoint, {
        //   json: this.data,
        // }).then(() => {
        //   // TODO: notify user that the record has been saved
        //   // console.log('saved...')
        // }).catch(error => {
        //   // TODO: notify user that there was an error saving the record
        //   // console.log('error saving...')
        // })
      }
    },

    provide() {
      return {
        styles: myStyles,
      }
    }
  })
</script>
