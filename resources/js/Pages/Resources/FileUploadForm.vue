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

      <div v-if="$page.props.flash.success" class="mx-12 text-success" :class="{ 'mt-4': hint }">
        <div>
          {{ $page.props.flash.success }}
        </div>
      </div>

      <div v-if="form.errors && Object.keys(form.errors).length > 0" class="mx-12 text-red" :class="{ 'mt-4': hint }">
        <ul>
          <li v-for="(error, index) in form.errors" :key="index" v-html="error.replace('\n', '<br />')"></li>
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
    uploadEndpoint: { type: String, required: true },
  })

  const form = useForm({
    files: props.multiple ? [] : null,
  })

  const onUpload = () => {
    form.post(props.uploadEndpoint, {
      onSuccess: (response) => {
        form.reset()
      }
    })
  }
</script>

<style lang="postcss">
  .v-file-input .v-input__control {
    @apply bg-white
  }
</style>
