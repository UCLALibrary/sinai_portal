const customStyles = {
  // extend defaults
  control: {
    root: 'flex flex-col gap-y-2',
    wrapper: 'flex-1',
    label: 'block text-sm font-medium text-gray-700 min-w-36',
    input: 'w-full border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white max-w-3xl',
    checkbox: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm cursor-pointer',
    textarea: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white',
    select: 'w-full border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white',
    error: 'text-xs text-red-500 ml-1',
    asterisk: 'text-red-500',
  },
  verticalLayout: {
    root: 'flex flex-col gap-y-2',
  },
  horizontalLayout: {
    root: 'flex gap-x-2',
  },
  group: {
    root: 'flex flex-col gap-y-4 mb-8',
    label: 'w-full text-xl font-semibold bg-transparent border-b pb-2 mb-4',
  },
  arrayList: {
    root: 'border border-gray-300 divide-y divide-gray-300',
    legend: 'flex flex-row-reverse w-full justify-between mb-2',
    addButton: 'mdi mdi-plus-circle-outline focus:outline-none focus:ring-2 focus:ring-sinai-red focus:border-sinai-red cursor-pointer mx-1',
    label: 'block text-sm font-medium text-gray-700',
    itemWrapper: '',
    noData: 'px-3 py-2 text-sm bg-gray-200',
    itemToolbar: '!m-0 p-2 border-b bg-gray-200',
    itemLabel: '!px-1 bg-transparent text-sm',
    itemContent: '!p-4',
    itemExpanded: '',
    itemMoveUp: 'mdi mdi-arrow-up-thin',
    itemMoveDown: 'mdi mdi-arrow-down-thin',
    itemDelete: 'mdi mdi-delete',
  },
  checkbox: {
    label: 'block text-sm font-medium text-gray-700 cursor-pointer'
  },
  // custom classes
  container: {
    label: 'flex items-center justify-between',
    checkbox: 'relative flex items-center gap-x-1 my-1',
    checkboxList: 'flex flex-wrap gap-x-4',
    arrayList: 'flex items-center gap-x-2',
  },
  label: {
    wrapper: 'flex items-center gap-x-2',
  },
  tooltip: {
    icon: 'mdi mdi-information-slab-circle-outline cursor-pointer',
  },
}

export default customStyles
