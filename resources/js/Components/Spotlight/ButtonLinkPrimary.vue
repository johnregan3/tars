<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({
    href: String,
});
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-2 justify-center rounded-md py-2 px-3 text-sm outline-offset-2 transition active:transition-none bg-slate-900/75 border-slate-100 font-semibold text-zinc-100 hover:bg-teal-500 active:bg-teal-500 active:text-slate-100/70 focus:bg-teal-500 disabled:opacity-25  disabled:cursor-not-allowed transition duration-300">
        <slot />
    </Link>
</template>
