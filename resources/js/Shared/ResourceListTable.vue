<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    v-for="(fieldName, index) in columns"
                    :key="index"
                    scope="col"
                    :class="{
                      'py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6': index === 0,
                      'px-3 py-3.5 text-left text-sm font-semibold text-gray-900': index !== 0
                    }">
                    {{ fieldName }}
                  </th>
                  <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">
                      Edit
                    </span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr
                  v-for="resource in resources"
                  :key="resource.id"
                  class="hover:bg-gray-100 focus-within:bg-gray-100">
                  <td
                    v-for="([key, label], index) in Object.entries(columns)"
                    :key="index"
                    class="whitespace-nowrap text-sm text-gray-900 p-0"
                    :class="{ 'font-medium': index === 0 }">
                    <Link :href="route($page.props.routes.edit, { resourceName: $page.props.resourceName, resourceId: resource.id })" tabindex="-1" class="block py-4"
                      :class="{
                        'pl-4 pr-3 sm:pl-6': index === 0,
                        'px-3': index !== 0
                      }">
                      <span v-html="resource[key]"></span>
                    </Link>
                  </td>
                  <td class="w-px relative whitespace-nowrap text-right text-sm font-medium p-0">
                    <Link :href="route($page.props.routes.edit, { resourceName: $page.props.resourceName, resourceId: resource.id })" tabindex="-1" class="block py-4 pl-3 pr-4 sm:pr-6">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="block w-6 h-6 fill-gray-400">
                        <polygon points="12.95 10.707 13.657 10 8 4.343 6.586 5.757 10.828 10 6.586 14.243 8 15.657 12.95 10.707" />
                      </svg>
                    </Link>
                  </td>
                </tr>
                <tr v-if="resources.length === 0">
                  <td class="px-6 py-4 border-t" :colspan="Object.keys(columns).length + 1">
                    No entries found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <Pagination
      :from="pagination.from"
      :to="pagination.to"
      :total="pagination.total"
      :links="pagination.links"
    />
  </div>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3'
  import Pagination from '@/Shared/Pagination.vue'

  defineProps({
    resources: { type: Array, required: false, default: () => [] },
    columns: { type: Object, required: false, default: () => [] },
    pagination: { type: Object, required: false, default: () => {} },
  })
</script>