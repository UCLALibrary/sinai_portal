<template>
  <el-icon
    :class="styles"
    @click="isDialogOpen = true">
    <Edit />
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
      mode="update"
      @on-save="onSave"
      @on-cancel="onCancel">
    </component>
  </el-dialog>
</template>

<script setup>
  import { defineAsyncComponent, ref, shallowRef, watch } from 'vue'

  const props = defineProps({
    title: { type: String, required: false, default: '' },
    resourceId: { type: Number, required: true },
    formEndpoint: { type: String, required: true },
    styles: { type: String, required: false, default: '' },
  })

  const emit = defineEmits(['on-save']);

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
      uischema.value = response.data.uischema
      data.value = response.data.data
    }
  }

  const clear = () => {
    formComponent.value = null
  }

  const onSave = (payload) => {
    // set the resource id in the payload
    payload.resourceId = props.resourceId

    emit('on-save', payload)
    isDialogOpen.value = false
  }

  const onCancel = () => {
    isDialogOpen.value = false
  }
</script>
