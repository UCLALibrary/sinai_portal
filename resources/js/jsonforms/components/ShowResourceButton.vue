<template>
  <el-icon
    :class="styles"
    @click="isDialogOpen = true">
    <View />
  </el-icon>

  <el-dialog
    :title="title"
    v-model="isDialogOpen"
    top="4vh">
    <component
      :is="formComponent"
      :schema="schema"
      :uischema="uischema"
      :data="data"
      mode="show"
      @on-cancel="onCancel">
    </component>
  </el-dialog>
</template>

<script setup>
  import { defineAsyncComponent, ref, shallowRef, watch } from 'vue'

  const props = defineProps({
    title: { type: String, required: false, default: '' },
    styles: { type: String, required: false, default: '' },
    formEndpoint: { type: String, required: true },
  })

  const isDialogOpen = ref(false)

  const formComponent = shallowRef(null)

  const schema = ref({})

  const uischema = ref({})

  const data = ref({})

  watch(() => isDialogOpen.value, (newValue) => {
    newValue ? initialize() : clear()
  })

  const initialize = async () => {
    const response = await axios.get(props.formEndpoint)
    if (response.data) {
      formComponent.value = defineAsyncComponent(() => import('@/jsonforms/components/ResourceForm.vue'))
      schema.value = response.data.schema

      // disable all form fields by adding "readonly" as a top-level form option
      uischema.value = response.data.uischema
      uischema.value.options = {
        readonly: true
      }

      data.value = response.data.data
    }
  }

  const clear = () => {
    formComponent.value = null
  }

  const onCancel = () => {
    isDialogOpen.value = false
  }
</script>
