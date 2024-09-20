import { ref, onBeforeMount } from 'vue'
import useUrlQueryParams from '@/composables/useUrlQueryParams'

export default function useSearchBox(searchQuery) {
  const { setUrlQueryParameter, deleteUrlQueryParameter } = useUrlQueryParams()

  const query = ref(null)

  onBeforeMount(async () => {
    // initialize the query from the url query parameters
    query.value = searchQuery
  })

  const onKeyup = (event) => {
    let inputQuery = event.target.value
    inputQuery !== ''
      ? setUrlQueryParameter('q', inputQuery)
      : deleteUrlQueryParameter('q')

    // update the state of the query ref to trigger the watcher
    query.value = inputQuery
  }

  const onReset = () => {
    deleteUrlQueryParameter('q')

    // update the state of the query ref to trigger the watcher
    query.value = ''
  }

  return {
    query,
    onKeyup,
    onReset,
  }
}
