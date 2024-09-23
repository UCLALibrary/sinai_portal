import { ref, computed, onBeforeMount } from 'vue'
import useUrlQueryParams from '@/composables/useUrlQueryParams'

export default function useRangeFilters(init) {
  const { setUrlQueryParameter, deleteUrlQueryParameter } = useUrlQueryParams()

  const filterQueryParamKey = 'ranges'

  // store the min and max range values for each filter category
  const minMaxRangeValues = ref(null)

  // transform the filter url query parameters into an object
  const rangeFilters = ref(null)

  // get the filter query parameters from the url
  const filterQueryParamString = computed(() => {
    return rangeFilters.value
      ? JSON.stringify(rangeFilters.value)
      : new URL(window.location.href).searchParams.get(filterQueryParamKey)
  })

  onBeforeMount(async () => {
    // initialize the min and max range values for each category
    minMaxRangeValues.value = await init()

    // transform the filter query parameters into an object
    const filterQueryParams = JSON.parse(filterQueryParamString.value)

    // set the range filters from the url query parameters
    rangeFilters.value = filterQueryParams ?? { ...minMaxRangeValues.value }
  })

  const onRangeFilter = (filterCategory, min, max) => {
    let status = applyFilter(filterCategory, [min, max])
    if (status) {
      if ((status.hasOwnProperty('filter-value-updated') && status['filter-value-updated']) || 
          (status.hasOwnProperty('filter-value-removed') && status['filter-value-removed'])) {
        setUrlQueryParameter(filterQueryParamKey, filterQueryParamString.value)
      }

      if (status.hasOwnProperty('filters-cleared') && status['filters-cleared']) {
        deleteUrlQueryParameter(filterQueryParamKey)
      }
    }
  }

  const onClearRangeFilters = () => {
    let status = clearFilters()
    if (status && status.hasOwnProperty('filters-cleared') && status['filters-cleared']) {
      deleteUrlQueryParameter(filterQueryParamKey)
    }
  }

  const applyFilter = (filterCategory, filterValue) => {
    let status = isDefaultFilterValue(filterCategory, filterValue)
      ? removeFilter(filterCategory, filterValue)
      : updateFilter(filterCategory, filterValue)

    // update the state of the filters ref to trigger any watchers
    rangeFilters.value = { ...rangeFilters.value }

    return status
  }

  const clearFilters = () => {
    // update the state of the filters ref to trigger any watchers
    rangeFilters.value = { ...minMaxRangeValues.value }

    return {
      'filters-cleared': true,
    }
  }

  const isDefaultFilterValue = (filterCategory, filterValue) => {
    return !isEmptyObject(rangeFilters.value) && rangeFilters.value[filterCategory] && JSON.stringify(filterValue) === JSON.stringify([minMaxRangeValues.value[filterCategory][0], minMaxRangeValues.value[filterCategory][1]])
  }

  const updateFilter = (filterCategory, filterValue) => {
    let status = {
      'filter-category-created': false,
      'filter-value-updated': false
    }

    if (!rangeFilters.value.hasOwnProperty(filterCategory)) {
      rangeFilters.value[filterCategory] = []
      status['filter-category-created'] = true
    }
    if (!rangeFilters.value[filterCategory].includes(filterValue)) {
      rangeFilters.value[filterCategory] = filterValue
      status['filter-value-updated'] = true
    }

    return status
  }

  const removeFilter = () => {
    // remove the filter property from the url query parameters when the default values are selected
    return {
      'filters-cleared': true,
    }
  }

  const isEmptyObject = (obj) => {
    return !obj || Object.keys(obj).length === 0
  }

  return {
    minMaxRangeValues,
    rangeFilters,
    onRangeFilter,
    onClearRangeFilters,
  }
}