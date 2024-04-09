<template>
  <AppLayout title="Users">
    <div class="lg:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mt-4 sm:flex sm:items-center px-4 sm:px-6 lg:px-8">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">
              Users
            </h1>
          </div>
          <div class="sm:ml-16 sm:mt-0 sm:flex-none">
            <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
              Add user
            </button>
          </div>
        </div>

        <ResourceListTable
          resource-name="users"
          :resources="users.data"
          :columns="['name', 'email']"
          :pagination="{
            from: users.from,
            to: users.to,
            total: users.total,
            links: users.links
          }"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ResourceListTable from '@/Shared/ResourceListTable.vue'

  defineProps({
    users: { type: Object, required: false, default: () => {} }
  })
</script>