<template>
  <AppLayout title="Users">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">
              Manuscripts
            </h1>
          </div>
          <div class="sm:ml-16 sm:mt-0 sm:flex-none">
            <button
              type="button"
              @click="redirectToUrl('/manuscripts/create')"
              class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              Add manuscript
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'

  const redirectToUrl = (url) => {
    window.location = url
  }
</script>