<script setup>
import Header from '@c/SpotHeader.vue'
import Footer from '@c/SpotFooter.vue'
defineProps({
  title: String
})
</script>
<template>
  <div>
    <div className="fixed inset-0 flex justify-center sm:px-8">
      <div className="flex w-full max-w-7xl lg:px-8">
        <div className="w-full ring-1 bg-slate-800/95 ring-slate-300/20" />
      </div>
    </div>
    <div className="relative w-[1215px] max-w-full">
      <Header />
      <main>
        <slot />
      </main>
      <Footer />
    </div>
  </div>
</template>
