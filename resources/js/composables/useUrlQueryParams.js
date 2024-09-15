export default function useUrlQueryParams() {
  const setUrlQueryParameter = (paramName, paramValue) => {
    const url = new URL(window.location.href)
    if (paramValue !== '') {
      url.searchParams.set(paramName, paramValue)
    }
    window.history.replaceState({}, '', url)
  }

  const deleteUrlQueryParameter = (paramName) => {
    const url = new URL(window.location.href)
    url.searchParams.delete(paramName)
    window.history.replaceState({}, '', url)
  }

  const clearUrlQueryParameters = () => {
    const url = new URL(window.location.href)
    url.search = ''
    window.history.replaceState({}, '', url)
  }

  return {
    setUrlQueryParameter,
    deleteUrlQueryParameter,
    clearUrlQueryParameters,
  }
}
