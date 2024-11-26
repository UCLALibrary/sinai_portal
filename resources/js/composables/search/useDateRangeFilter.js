import { ref, computed, onBeforeMount } from 'vue'
import useRangeFilters from '@/composables/search/useRangeFilters'

export default function useDateRangeFilter(index) {
  const {
    minMaxRangeValues,
    rangeFilters,
    onRangeFilter,
    onClearRangeFilters,
  } = useRangeFilters()

  const showDateRangeFilter = ref(false)

  const getMinMaxDateRangeValues = async () => {
    const results = await index.search('', {
      hitsPerPage: 2000,
      attributesToRetrieve: ['date_min', 'date_max'],
    })

    // set the min and max date range values
    let dateMin = Number.MAX_SAFE_INTEGER
    let dateMax = Number.MIN_SAFE_INTEGER
    for (const hit of results.hits) {
      if (hit.date_min && hit.date_min < dateMin) {
        dateMin = hit.date_min
      }
      if (hit.date_max && hit.date_max > dateMax) {
        dateMax = hit.date_max
      }
    }

    return dateMin === Number.MAX_SAFE_INTEGER || dateMax === Number.MIN_SAFE_INTEGER
      ? null
      : { date: [dateMin, dateMax] }
  }

  onBeforeMount(async () => {
    // get the min and max date range values from the 'date_min' and 'date_max' fields in the search index
    const minMaxDates = await getMinMaxDateRangeValues()

    if (minMaxDates) {
      minMaxRangeValues.value = minMaxDates

      // initialize the date range values from the search index when no date filter has already been applied
      if (rangeFilters.value && Object.keys(rangeFilters.value).length === 0) {
        rangeFilters.value = { ...minMaxRangeValues.value }
      }

      showDateRangeFilter.value = true
    }
  })

  const dateRangeFilterQuery = computed(() => {
    if (rangeFilters.value && minMaxRangeValues.value) {
      const minSelected = rangeFilters.value.date[0]
      const maxSelected = rangeFilters.value.date[1]
      const minDefault = minMaxRangeValues.value.date[0]
      const maxDefault = minMaxRangeValues.value.date[1]

      if (minSelected === minDefault && maxSelected === maxDefault) {
        return null
      } else {
        const min = minSelected || minDefault
        const max = maxSelected || maxDefault
        return `(date_max >= ${min} AND date_min <= ${max})`
      }
    }
    return null
  })

  const isDateRangeFilterApplied = computed(() => {
    if (!rangeFilters.value || !minMaxRangeValues.value) {
      return false
    }

    const minSelected = rangeFilters.value.date[0]
    const maxSelected = rangeFilters.value.date[1]
    const minDefault = minMaxRangeValues.value.date[0]
    const maxDefault = minMaxRangeValues.value.date[1]

    return minSelected !== minDefault || maxSelected !== maxDefault
  })

  return {
    showDateRangeFilter,
    minMaxRangeValues,
    rangeFilters,
    onRangeFilter,
    onClearRangeFilters,
    dateRangeFilterQuery,
    isDateRangeFilterApplied,
  }
}