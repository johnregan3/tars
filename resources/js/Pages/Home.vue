<script setup>
import { Link } from "@inertiajs/vue3";
import Spotlight from "@/Layouts/Spotlight.vue";
import ButtonLinkPrimary from "@/Components/Spotlight/ButtonLinkPrimary.vue";

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
});
</script>
<template>
  <Spotlight title="Welcome">
    <main>
      <div class="sm:px-8 mt-16 sm:mt-32">
        <div class="mx-auto max-w-7xl lg:px-8">
          <div class="relative px-4 sm:px-8 lg:px-12">
            <div class="mx-auto max-w-2xl lg:max-w-5xl">
              <div
                class="mt-6 space-y-7 text-base text-zinc-600 text-center dark:text-zinc-400"
              >
                <ButtonLinkPrimary
                  :href="route('chat.index')"
                  class="btn btn-primary px-8 py-4 text-xl bg-teal-500 hover:bg-teal-600"
                >
                  <span>Chat with TARS</span>
                  <font-awesome-icon icon="fa-solid fa-arrow-right-long" />
                </ButtonLinkPrimary>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </Spotlight>
</template>
