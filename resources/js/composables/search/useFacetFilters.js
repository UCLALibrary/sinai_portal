import { ref, computed, onBeforeMount } from 'vue'
import useUrlQueryParams from '@/composables/useUrlQueryParams'

export default function useFacetFilters() {
  const { setUrlQueryParameter, deleteUrlQueryParameter } = useUrlQueryParams()

  const filterQueryParamKey = 'filters'

  // transform the filter url query parameters into an array of objects
  const filters = ref(null)

  // get the filter query parameters from the url
  const filterQueryParamString = computed(() => {
    return filters.value
      ? deserializeFilterQueryParameters(filters.value)
      : new URL(window.location.href).searchParams.get(filterQueryParamKey)
  })

  onBeforeMount(async () => {
    // transform the filter query parameters into an array
    const filterQueryParams = JSON.parse(filterQueryParamString.value)

    // serialize the filter query parameters into an object
    filters.value = serializeFilterQueryParameters(filterQueryParams)
  })

  const onFilter = (filterCategory, filterValue) => {
    let status = applyFilter(filterCategory, filterValue)
    if (status) {
      if ((status.hasOwnProperty('filter-value-added') && status['filter-value-added']) || 
          (status.hasOwnProperty('filter-value-removed') && status['filter-value-removed'])) {
        setUrlQueryParameter(filterQueryParamKey, filterQueryParamString.value)
      }

      if (status.hasOwnProperty('filters-cleared') && status['filters-cleared']) {
        deleteUrlQueryParameter(filterQueryParamKey)
      }
    }
  }

  const onClearFilters = (refine) => {
    refine()
    let status = clearFilters()
    if (status && status.hasOwnProperty('filters-cleared') && status['filters-cleared']) {
      deleteUrlQueryParameter(filterQueryParamKey)
    }
  }

  const applyFilter = (filterCategory, filterValue) => {
    let status = isFilterApplied(filterCategory, filterValue)
      ? removeFilter(filterCategory, filterValue)
      : addFilter(filterCategory, filterValue)

    // update the state of the filters ref to trigger the watcher
    filters.value = { ...filters.value }

    return status
  }

  const clearFilters = () => {
    // update the state of the filters ref to trigger the watcher
    filters.value = {}

    return {
      'filters-cleared': true,
    }
  }

  const isFilterApplied = (filterCategory, filterValue) => {
    return !isEmptyObject(filters.value) && filters.value[filterCategory] ? filters.value[filterCategory].includes(filterValue) : false
  }

  const addFilter = (filterCategory, filterValue) => {
    let status = {
      'filter-category-created': false,
      'filter-value-added': false
    }

    if (!filters.value.hasOwnProperty(filterCategory)) {
      filters.value[filterCategory] = []
      status['filter-category-created'] = true
    }
    if (!filters.value[filterCategory].includes(filterValue)) {
      filters.value[filterCategory].push(filterValue)
      status['filter-value-added'] = true
    }

    return status
  }

  const removeFilter = (filterCategory, filterValue) => {
    let status = {
      'filter-category-deleted': false,
      'filter-value-removed': false,
      'filters-cleared': false,
    }

    const indexToRemove = !isEmptyObject(filters.value) ? filters.value[filterCategory].indexOf(filterValue) : -1
    if (indexToRemove !== -1) {
      // remove the element at the specified index
      filters.value[filterCategory].splice(indexToRemove, 1)
      status['filter-value-removed'] = true

      // remove the filter category when there are no values
      if (filters.value[filterCategory].length === 0) {
        delete filters.value[filterCategory]
        status['filter-category-deleted'] = true
      }

      // remove the filter property from the url query parameters when there are no values
      if (isEmptyObject(filters.value)) {
        status['filters-cleared'] = true
      }
    }

    return status
  }

  /*
   * Transforms the following data structure:
   *
   *   [
   *     "country:Bahamas",
   *     "country:Ecuador",
   *     "disciplines:Asian American Studies",
   *     "languages:Bengali",
   *     "scholar_inquiry_categories:Mentoring"
   *   ]
   *
   * into:
   *
   *   {
   *     country: ["Bahamas", "Ecuador"],
   *     disciplines: ["Asian American Studies"],
   *     languages: ["Bengali"], 
   *     scholar_inquiry_categories: ["Mentoring"]
   *   }
   *
   */
  const serializeFilterQueryParameters = (filterQueryParams) => {
    const object = {}
    if (!isEmptyObject(filterQueryParams)) {
      filterQueryParams.forEach(filterQuery => {
        const [key, value] = filterQuery.split(':')
        if (!object[key]) {
          object[key] = []
        }
        object[key].push(value)
      })
    }
    return object
  }

  /*
   * Transforms the following data structure:
   *
   *   {
   *     country: ["Bahamas", "Ecuador"],
   *     disciplines: ["Asian American Studies"],
   *     languages: ["Bengali"], 
   *     scholar_inquiry_categories: ["Mentoring"]
   *   }
   *
   * into a url query parameter string:
   *
   *   ["country:Bahamas","country:Ecuador","disciplines:Asian American Studies","languages:Bengali","scholar_inquiry_categories:Mentoring"]
   *
   */
  const deserializeFilterQueryParameters = (filters) => {
    const filterQueryParam = Object.entries(filters)
      .map(([key, values]) => values.map(value => `${key}:${value}`))
      .flat()

    return JSON.stringify(filterQueryParam)
  }

  const isEmptyObject = (obj) => {
    return !obj || Object.keys(obj).length === 0
  }

  return {
    filters,
    onFilter,
    onClearFilters,
  }
}