import { useStyles } from '@jsonforms/vue-vanilla/src/styles'
import { computed, ref } from 'vue'
import merge from 'lodash/merge'
import cloneDeep from 'lodash/cloneDeep'

/**
 * Adds styles, isFocused, appliedOptions and onChange
 */
export const useCustomVanillaControl = <
  I extends { control: any; handleChange: any }
>(
  input: I,
  adaptTarget: (target: any) => any = (v) => v
) => {
  const appliedOptions = computed(() =>
    merge(
      {},
      cloneDeep(input.control.value.config),
      cloneDeep(input.control.value.uischema.options)
    )
  )

  const isFocused = ref(false)
  const onChange = (value: any) => {
    input.handleChange(input.control.value.path, adaptTarget(value))
  }

  const controlWrapper = computed(() => {
    const { id, description, errors, label, visible, required } =
      input.control.value
    return { id, description, errors, label, visible, required }
  })

  return {
    ...input,
    styles: useStyles(input.control.value.uischema),
    isFocused,
    appliedOptions,
    controlWrapper,
    onChange,
  }
}
