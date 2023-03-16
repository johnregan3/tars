<script setup>
import { onMounted, ref } from 'vue'

defineProps({
  modelValue: String
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value.focus() })
</script>
<template>
  <textarea
    ref="input"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    placeholder="Type your message here..."
    class="min-w-0 h-32 flex-auto appearance-none rounded-md border px-3 py-[calc(theme(spacing.2)-1px)] text-zinc-200 focus:outline-none focus:ring-4 border-slate-600 bg-slate-700/70 shadow-[inset_0px_00px_6px_0px_rgba(24,24,27,0.3)] placeholder:text-zinc-500 focus:border-teal-400 focus:ring-teal-400/10 sm:text-sm disabled:cursor-not-allowed disabled:bg-zinc-700/[0.15] disabled:text-zinc-500"
  />
</template>
