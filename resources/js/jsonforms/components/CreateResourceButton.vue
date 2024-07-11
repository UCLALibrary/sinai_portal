<template>
  <button
    type="button"
    :class="styles"
    @click="isDialogOpen = true">
  </button>

  <el-dialog
    :title="title"
    v-model="isDialogOpen">
    <component
      :is="formComponent"
      :schema="schema"
      :uischema="uischema"
      :data="data"
      @on-save="onSave"
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
      formComponent.value = defineAsyncComponent(() => import('@/jsonforms/components/CreateEditForm.vue'))
      schema.value = response.data.schema
      uischema.value = response.data.uischema
    }
  }

  const clear = () => {
    formComponent.value = null
  }

  const onSave = (payload) => {
    emit('on-save', payload.data)
    isDialogOpen.value = false
  }

  const onCancel = () => {
    isDialogOpen.value = false
  }
</script>
