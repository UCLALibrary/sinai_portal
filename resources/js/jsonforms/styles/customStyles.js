const customStyles = {
  // extend defaults
  control: {
    root: 'flex flex-col gap-y-2',
    wrapper: 'wrapper flex flex-1 items-center gap-x-2',
    label: 'flex text-sm font-medium text-gray-700 min-w-44',
    input: 'flex border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white w-full max-w-3xl disabled:cursor-not-allowed disabled:!text-gray-500',
    checkbox: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm cursor-pointer',
    textarea: 'border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white w-full ml-2 max-w-3xl',
    select: 'w-full border border-gray-300 rounded-md shadow-sm focus:ring-sinai-red focus:border-sinai-red sm:text-sm bg-white max-w-3xl',
    error: 'text-xs text-red-500 ml-1',
    asterisk: 'ml-0.5 text-red-500',
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
    addButton: 'h-8 mt-8 text-xl hover:text-sinai-red mdi mdi-plus-circle-outline focus:outline-none focus:ring-2 focus:ring-sinai-red focus:border-sinai-red cursor-pointer mx-1',
    label: 'mt-8 block text-lg font-medium text-gray-700',
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
    label: 'flex items-start',
    checkbox: 'flex items-center gap-x-1 my-1',
    checkboxList: 'grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 max-w-max',
    arrayList: 'flex items-center gap-x-2',
  },
  label: {
    wrapper: 'flex items-center gap-x-2',
  },
  tooltip: {
    icon: 'text-base mdi mdi-information-slab-circle-outline cursor-pointer hover:text-sinai-red',
  },
}

export default customStyles
