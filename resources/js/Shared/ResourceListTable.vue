<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="mt-8 flow-root">
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
                    }"
                  >
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
                <tr v-for="resource in resources" :key="resource.id">
                  <td
                    v-for="(column, index) in columns"
                    :key="index"
                    :class="{
                      'whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6': index === 0,
                      'whitespace-nowrap px-3 py-4 text-sm text-gray-500': index !== 0
                    }"
                  >
                    <span v-html="resource[column]"></span>
                  </td>
                  <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <a href="#" class="text-indigo-600 hover:text-indigo-900">
                      Edit<span class="sr-only">, {{ resource.name }}</span>
                    </a>
                  </td>
                </tr>
                <tr v-if="resources.length === 0">
                  <td class="px-6 py-4 border-t" :colspan="columns.length + 1">
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
  import Pagination from '@/Shared/Pagination.vue'

  defineProps({
    resourceName: { type: String, required: true },
    resources: { type: Array, required: false, default: () => [] },
    columns: { type: Array, required: false, default: () => [] },
    pagination: { type: Object, required: false, default: () => {} },
  })
</script>