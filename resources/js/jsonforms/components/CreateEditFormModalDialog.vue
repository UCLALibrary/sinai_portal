<template>
  <v-dialog
    v-model="dialog"
    max-width="800">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn
        prepend-icon="mdi-plus"
        :text="title"
        v-bind="activatorProps">
      </v-btn>
    </template>

    <v-container class="bg-white">
      <v-row class="p-4 border-b">
        <v-col>
          <h1 class="text-2xl font-semibold leading-6 text-gray-900">
            {{ title }}
          </h1>
        </v-col>
      </v-row>

      <v-row>
        <v-col>
          <component
            :is="formComponent"
            :schema="schema"
            :uischema="uischema"
            :data="data"
            @on-save="onSave"
            @on-cancel="onCancel">
          </component>
        </v-col>
      </v-row>
    </v-container>
  </v-dialog>
</template>

<script setup>
  import { defineAsyncComponent, ref, shallowRef, watch } from 'vue'

  const props = defineProps({
    title: { type: String, required: true },
    contentEndpoint: { type: String, required: true },
  })

  const emit = defineEmits(['on-save']);

  const dialog = ref(null)

  const formComponent = shallowRef(null)

  const schema = ref({})

  const uischema = ref({})

  const data = ref({})

  watch(() => dialog.value, (newValue) => {
    newValue ? initialize() : clear()
  })

  const initialize = async () => {
    const response = await axios.get(props.contentEndpoint)
    if (response.data) {
      formComponent.value = defineAsyncComponent(() => import('@/jsonforms/components/CreateEditForm.vue'))
      schema.value = response.data.schema
      uischema.value = response.data.uischema
    }
  }

  const clear = () => {
    formComponent.value = null
  }

  const onSave = (jsonData) => {
    emit('on-save', jsonData)
    dialog.value = false
  }

  const onCancel = () => {
    dialog.value = false
  }
</script>
