<template>
  <div class="flex items-center justify-between mt-2 mb-4 px-1">
    <AisStats v-slot="{ nbHits }">
      {{ nbHits }} result<span v-if="nbHits !== 1">s</span> found
    </AisStats>

    <DownloadButton
      v-if="$page.props.auth.user && $page.props.roles.permissions.includes('download records')"
      :endpoint="route('frontend.files.download.zip', { recordType })"
    />
  </div>
</template>

<script setup>
  import { AisStats } from 'vue-instantsearch/vue3/es'
  import DownloadButton from '@/Shared/Buttons/DownloadButton.vue'

  defineProps({
    recordType: { type: String, required: true },
  })
</script>
