<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center gap-2 justify-center rounded-md py-2 px-3 text-sm outline-offset-2 transition active:transition-none bg-zinc-700 font-semibold text-zinc-100 hover:bg-zinc-600 active:bg-zinc-700 active:text-zinc-100/70 disabled:opacity-25 transition duration-300">
        <slot />
    </button>
</template>
