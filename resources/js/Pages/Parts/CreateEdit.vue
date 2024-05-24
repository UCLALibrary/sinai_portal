<template>
  <AppLayout title="Add Codicological Unit">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Codicological Units > Add Codicological Unit
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
  import { JsonForms } from '@jsonforms/vue'
  import {
    defaultStyles,
    mergeStyles,
    vuetifyRenderers
  } from '@jsonforms/vue-vuetify'
  import AppLayout from '@/Layouts/AppLayout.vue'

  const renderers = [
    ...vuetifyRenderers,
    // custom renderers
  ]

  const uischema = {
    type: 'VerticalLayout',
    elements: [
      {
        type: 'Group',
        label: 'Basic Metadata',
        elements: [
          {
            type: 'Control',
            scope: '#/properties/id',
          },
          {
            type: 'Control',
            scope: '#/properties/ark',
          },
        ]
      },
    ]
  }

  const schema = ref(
    {
      // "$schema": "https://json-schema.org/draft/2020-12/schema",
      "$id": "https://raw.githubusercontent.com/UCLALibrary/sinai_metadata/master/data-model/jsonschemas/cod_unit.json",
      "title": "Codicological Unit",
      "description": "A codicological unit represents a distinctly produced book (REVISE)",
      "type": "object",
      "properties": {
        "id": {
          "title": "Identifier",
          "description": "A unique identifer, supplied by the database",
          "type": "string"  // [DOUGKIM] CHANGED FROM "integer" TO "string"
        },
        "ark": {
          "title": "ARK Identifier",
          "description": "A unique Archival Resource Key (ARK) describing the manuscript object",
          "type": "string",
          "pattern": "^ark:/21198/z1.{6}",
        },
      },
      "required": [
        "id",
        "ark",
      ]
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

    data() {
      return {
        renderers: Object.freeze(renderers),
        data,
        schema,
        uischema,
      }
    },

    mounted() {
      console.log(this.data)
    },

    methods: {
      onChange(event) {
        this.data = event.data
      },

      onSave() {
        axios.post(route('api.codicological-units.store'), {
          json: this.data,
        }).then(() => {
          // TODO: notify user that the record has been saved
          // console.log('saved...')
        }).catch(error => {
          // TODO: notify user that there was an error saving the record
          // console.log('error saving...')
        })
      }
    },

    provide() {
      return {
        styles: myStyles,
      }
    }
  })
</script>
