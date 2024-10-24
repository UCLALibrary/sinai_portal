<template>
  <v-form class="flex space-x-4" @submit.prevent="onUpload">
    <div class="flex flex-col w-full">
      <v-file-input
        @change="form.clearErrors()"
        v-model="form.files"
        :label="label"
        :hint="hint"
        persistent-hint
        :multiple="multiple"
        clearable
        chips
        variant="outlined">
      </v-file-input>

      <div v-if="form.errors && Object.keys(form.errors).length > 0" class="mx-12 text-red" :class="{ 'mt-4': hint }">
        <ul>
          <li v-for="(errors, key) in form.errors" :key="key">
            {{ errors[0] }}
          </li>
        </ul>
      </div>
    </div>

    <v-btn
      type="submit"
      color="primary"
      class="mt-2">
      Upload
    </v-btn>
  </v-form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'

  const props = defineProps({
    label: { type: String, required: false, default: '' },
    multiple: { type: Boolean, required: false, default: true },
    hint: { type: String, required: false, default: '' },
    endpoint: { type: String, required: true },
  })

  const emit = defineEmits(['on-success', 'on-error'])

  const form = useForm({
    files: props.multiple ? [] : null,
  })

  const onUpload = () => {
    const formData = new FormData()

    if (props.multiple) {
      form.files.forEach(file => formData.append('files[]', file))
    } else {
      formData.append('files', form.files)
    }

    axios.post(props.endpoint, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
      .then(response => {
        form.reset()
        emit('on-success', response.data)
      })
      .catch(error => {
        form.errors = error.response.data.errors
        emit('on-error', error.response.data.errors)
      })
  }
</script>

<style lang="postcss">
  .v-file-input .v-input__control {
    @apply bg-white
  }
</style>
