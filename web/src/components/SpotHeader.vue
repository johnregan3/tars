<script setup>
import NavMain from '@c/SpotNavMain.vue'
</script>
<template>
  <header
    class="pointer-events-none relative z-50 flex flex-col"
    style="height: var(--header-height); margin-bottom: var(--header-mb)"
  >
    <div class="top-0 z-10 h-16 pt-6" style="position: var(--header-position)">
      <div
        class="sm:px-8 top-[var(--header-top,theme(spacing.6))] w-full"
        style="position: var(--header-inner-position)"
      >
        <div class="mx-auto max-w-7xl lg:px-8">
          <div class="relative px-4 sm:px-8 lg:px-12">
            <div class="mx-auto max-w-2xl lg:max-w-5xl">
              <div class="relative flex gap-4">
                <div class="group flex flex-1 items-center">
                  <div
                    class="flex items-center justify-between h-10 w-10 p-0.5 shadow-lg shadow-zinc-800/5 ring-none backdrop-blur group-hover:ring-2 group-hover:ring-teal-400 rounded-full transition duration-500 ease-in-out"
                  >
                    <img
                      src="@a/icon-tars.svg"
                      class="rounded rounded-full border-2 border-transparent bg-slate-300 group-hover:bg-zinc-300 transition duration-500 ease-in-out"
                      style="color: transparent"
                      width="512"
                      height="512"
                    />
                  </div>
                  <span class="pointer-events-auto text-white font-bold text-2xl ml-2">TARS</span>
                </div>
                <div class="flex flex-1 justify-end text-white">
                  <NavMain />
                </div>
                <!-- <div class="flex justify-end md:flex-1 text-white">Button</div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
