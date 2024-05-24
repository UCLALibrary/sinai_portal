<template>
  <AppLayout title="Add Manuscript">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Manuscripts > Add Manuscript
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
          {
            type: 'Control',
            scope: '#/properties/shelfmark',
          },
        ]
      },
    ]
  }

  const schema = ref(
    {
      // "$schema": "https://json-schema.org/draft/2020-12/schema",  // [DOUGKIM] COMMENTED OUT TO FIX ISSUES LOADING JSON SCHEMA INTO JSONFORMS
      "$id": "https://example.com/ms-obj.schema.json",
      "title": "Manuscript Object",
      "description": "A manuscript or codex, either real or a hypothetical reconstruction",
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
        "shelfmark": {
          "title": "Shelfmark",
          "description": "An identifier for the manuscript, including shelfmarks or other identifier schemas",
          "type": "string",
        },
      },
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

    methods: {
      onChange(event) {
        this.data = event.data
      },

      onSave() {
        axios.post(route('api.manuscripts.store'), {
          json: this.data,
        }).then(() => {
          // TODO: notify user that the manuscript has been saved
          // console.log('saved...')
        }).catch(error => {
          // TODO: notify user that there was an error saving the manuscript
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
