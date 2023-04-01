<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ref } from 'vue'
import Button from '@c/SpotButton.vue'
import ButtonPrimary from '@c/SpotButtonPrimary.vue'

defineProps({
  settingsOpen: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <TransitionRoot as="template" :show="true">
    <Dialog as="div" class="relative z-[50]" @close="settingsOpen = false">
      <div class="fixed inset-0" />

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-sm">
                <div
                  class="flex h-full flex-col overflow-y-scroll bg-slate-800 text-slate-400 py-7 shadow-xl"
                >
                  <div class="px-4 sm:px-6">
                    <div class="flex items-start justify-between">
                      <DialogTitle class="text-lg leading-6 text-slate-100"> Settings </DialogTitle>
                      <div class="ml-3 flex h-7 items-center">
                        <button
                          type="button"
                          class="rounded-full px-6 py-2 bg-zinc-800/90 text-zinc-200 hover:text-teal-400 focus:outline-none ring-1 ring-white/10"
                          @click="open = false"
                        >
                          <span class="">Close</span>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="relative mt-6 flex-1 px-4 sm:px-6">
                    <div class="mb-8">
                      <div class="mb-4 flex items-center justify-between">
                        <label for="range" class="block text-sm font-medium leading-6"
                          >Max Length</label
                        >
                        <input
                          type="number"
                          min="0"
                          max="4000"
                          step="1"
                          value="256"
                          class="text-xs text-right w-12 py-1 px-2 rounded-md border text-zinc-200 bg-slate-700/70 focus:outline-none focus:ring-4 placeholder:text-zinc-500 border-slate-600 focus:border-teal-400"
                        />
                      </div>
                      <div class="mt-2">
                        <input
                          type="range"
                          name="range"
                          id="range"
                          min="1"
                          max="4000"
						  value="256"
                          class="block w-full rounded-md appearance-none cursor-pointer h-2 bg-gray-700 border-0 shadow-sm ring-1 ring-inset ring-slate-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          aria-describedby="range-description"
                        />
                      </div>
                      <p class="mt-2 text-sm text-gray-500" id="range-description">
                        Default 400. The maximum number of tokens in the reply. 100 words is around
                        75 tokens.
                      </p>
                    </div>
                    <div class="mb-8">
                      <div class="mb-4 flex items-center justify-between">
                        <label for="range" class="block text-sm font-medium leading-6"
                          >Temperature</label
                        >
                        <input
                          type="number"
                          min="0"
                          max="1"
                          step="0.01"
                          value="0.75"
                          class="text-xs text-right w-12 py-1 px-2 rounded-md border text-zinc-200 bg-slate-700/70 focus:outline-none focus:ring-4 placeholder:text-zinc-500 border-slate-600 focus:border-teal-400"
                        />
                      </div>
                      <div class="mt-2">
                        <input
                          type="range"
                          name="range"
                          id="range"
                          min="0"
                          max="1"
                          step="0.01"
                          value="0.75"
                          class="block w-full rounded-md appearance-none cursor-pointer h-2 bg-gray-700 border-0 shadow-sm ring-1 ring-inset ring-slate-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          aria-describedby="range-description"
                        />
                      </div>
                      <p class="mt-2 text-sm text-gray-500" id="range-description">
                        Default 0. If generating a reply was a recipe, temperature would be the
                        spice. The higher the temperature, the more interesting reply. Don't modify
                        this and Top P at the same time.
                      </p>
                    </div>
                    <div class="mb-8">
                      <div class="mb-4 flex items-center justify-between">
                        <label for="range" class="block text-sm font-medium leading-6"
                          >Top P</label
                        >
                        <input
                          type="number"
                          min="0"
                          max="1"
                          step="0.01"
                          value="0.75"
                          class="text-xs text-right w-12 py-1 px-2 rounded-md border text-zinc-200 bg-slate-700/70 focus:outline-none focus:ring-4 placeholder:text-zinc-500 border-slate-600 focus:border-teal-400"
                        />
                      </div>
                      <div class="mt-2">
                        <input
                          type="range"
                          name="range"
                          id="range"
                          min="0"
                          max="1"
                          step="0.01"
                          value="0.75"
                          class="block w-full rounded-md appearance-none cursor-pointer h-2 bg-gray-700 border-0 shadow-sm ring-1 ring-inset ring-slate-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          aria-describedby="range-description"
                        />
                      </div>
                      <p class="mt-2 text-sm text-gray-500" id="range-description">
                        Default 1. If generating a reply was a recipe, the Top P would be the
                        ingredients you use. The higher the Top P, the more interesting reply. Don't
                        modify this and the temperature at the same time.
                      </p>
                    </div>

                    <div class="mb-8">
                      <div class="mb-4 flex items-center justify-between">
                        <label for="range" class="block text-sm font-medium leading-6"
                          >Frequency Penalty</label
                        >
                        <input
                          type="number"
                          min="0"
                          max="2"
                          step="0.01"
                          value="0.75"
                          class="text-xs text-right w-12 py-1 px-2 rounded-md border text-zinc-200 bg-slate-700/70 focus:outline-none focus:ring-4 placeholder:text-zinc-500 border-slate-600 focus:border-teal-400"
                        />
                      </div>
                      <div class="mt-2">
                        <input
                          type="range"
                          name="range"
                          id="range"
                          min="0"
                          max="2"
                          step="0.01"
                          value="0.75"
                          class="block w-full rounded-md appearance-none cursor-pointer h-2 bg-gray-700 border-0 shadow-sm ring-1 ring-inset ring-slate-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          aria-describedby="range-description"
                        />
                      </div>
                      <p class="mt-2 text-sm text-gray-500" id="range-description">
                        Default 0. Decreases the likelihood of repeatedly using the same phrase.
                      </p>
                    </div>
                    <div class="mb-8">
                      <div class="mb-4 flex items-center justify-between">
                        <label for="range" class="block text-sm font-medium leading-6"
                          >Presence Penalty</label
                        >
                        <input
                          type="number"
                          min="0"
                          max="2"
                          step="0.01"
                          value="0.75"
                          class="text-xs text-right w-12 py-1 px-2 rounded-md border text-zinc-200 bg-slate-700/70 focus:outline-none focus:ring-4 placeholder:text-zinc-500 border-slate-600 focus:border-teal-400"
                        />
                      </div>
                      <div class="mt-2">
                        <input
                          type="range"
                          name="range"
                          id="range"
                          min="0"
                          max="2"
                          step="0.01"
                          value="0.75"
                          class="block w-full rounded-md appearance-none cursor-pointer h-2 bg-gray-700 border-0 shadow-sm ring-1 ring-inset ring-slate-800 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          aria-describedby="range-description"
                        />
                      </div>
                      <p class="mt-2 text-sm text-gray-500" id="range-description">
                        Default 0. Decreases the likelihood of the reply to stay on topic.
                      </p>
                    </div>
                    <div>
                      <ButtonPrimary class="mr-4">Save</ButtonPrimary>
                      <Button>Undo All</Button>
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<style scoped>
input[type='range']::-webkit-slider-thumb,
input[type='range']::-moz-range-thumb {

  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  cursor: ew-resize;
  background: #1e293b;
  border-radius: 50%;
}
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type='number'] {
  -moz-appearance: textfield;
}
</style>
