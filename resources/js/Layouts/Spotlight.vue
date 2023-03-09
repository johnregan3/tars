<script setup>
import { Head } from "@inertiajs/vue3";
import Header from "@/Components/Spotlight/Header.vue";
import Footer from "@/Components/Spotlight/Footer.vue";
defineProps({
  title: String,
});
</script>
<template>
  <div>
    <Head :title="title" />
    <div className="fixed inset-0 flex justify-center sm:px-8">
      <div className="flex w-full max-w-7xl lg:px-8">
        <div className="w-full ring-1 bg-slate-800/95 ring-slate-300/20" />
      </div>
    </div>
    <div className="relative">
      <Header />
      <main>
        <slot />
      </main>
      <!-- <Footer /> -->
    </div>
  </div>
</template>
