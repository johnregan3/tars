<script setup>
import { Link } from "@inertiajs/vue3";
import NavMain from "@/Components/Spotlight/NavMain.vue";
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
                <div class="flex flex-1">
                  <div
                    class="h-10 w-10 rounded-full bg-white/90 p-0.5 shadow-lg shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90 dark:ring-white/10"
                  >
                    <Link aria-label="Home" class="pointer-events-auto" :href="route('home')"><img
                        src="/img/apoplexik-sq.jpg"
                        class="rounded-full bg-zinc-100 object-cover dark:bg-zinc-800 h-9 w-9"
                        style="color: transparent"
                        width="512"
                        height="512"
                    /></Link>
                  </div>
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
