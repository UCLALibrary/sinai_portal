import { ref } from 'vue'
import axios from 'axios'
 
export default function useResourcesApi(resourceName) {
  const apiEndpointBasePath = `/api/${resourceName}`

  const resource = ref([])
  const resources = ref([])

  const errors = ref('')

  const getResources = async () => {
    let response = await axios.get(apiEndpointBasePath)
    resources.value = response.data.data
  }

  const getResource = async (id) => {
    let response = await axios.get(`${apiEndpointBasePath}/${id}`)
    resource.value = response.data.data
  }

  const storeResource = async (data) => {
    errors.value = ''
    try {
      await axios.post(apiEndpointBasePath, data)
    } catch (e) {
      if (e.response.status === 422) {
        for (const key in e.response.data.errors) {
          errors.value = e.response.data.errors
        }
      }
    }
  }

  const updateResource = async (id) => {
    errors.value = ''
    try {
      await axios.patch(`${apiEndpointBasePath}/${id}`, resource.value)
    } catch (e) {
      if (e.response.status === 422) {
        for (const key in e.response.data.errors) {
          errors.value = e.response.data.errors
        }
      }
    }
  }

  const destroyResource = async (id) => {
    await axios.delete(`${apiEndpointBasePath}/${id}`)
  }

  return {
    errors,
    resource,
    resources,
    getResource,
    getResources,
    storeResource,
    updateResource,
    destroyResource,
  }
}
