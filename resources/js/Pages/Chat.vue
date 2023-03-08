<script setup>
import { Link, useForm, router } from "@inertiajs/vue3";
import Spotlight from "@/Layouts/Spotlight.vue";
import ButtonPrimary from "@/Components/Spotlight/ButtonPrimary.vue";
import Textarea from "@/Components/Spotlight/Textarea.vue";
import { ref } from "vue";
defineProps({
  data: Object,
});

let form = useForm({
  message: "",
});

const messagesList = ref(null);

let sendMessage = () => {
  form.post(route("chat.store"), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ preserveState: true });
      form.reset();
      messagesList.value.scrollTop = messagesList.value.scrollHeight;
    },
  });
};
</script>

<template>
  <Spotlight title="Chat">
    <div class="sm:px-8 mt-16 sm:mt-20">
      <div class="mx-auto max-w-7xl lg:px-8">
        <div class="relative px-4 sm:px-8 lg:px-12">
          <div class="mx-auto max-w-2xl lg:max-w-5xl">
            <div class="grid grid-cols-1 gap-y-16">
              <div class="lg:order-first lg:row-span-2">
                <div
                  class="relative w-full mx-auto mt-6 space-y-7 text-base text-zinc-400 after:absolute after:top-0 after:w-full after:block after:h-8 after:content[''] after:z-1 after:bg-gradient-to-b after:from-zinc-900 after:to-transparent before:pointer-events-none"
                >
                  <ul
                    ref="messagesList"
                    role="list"
                    class="max-h-128 px-2 relative overflow-scroll"
                  >
                    <li
                      v-for="memory in data.memories"
                      :key="memory.id"
                      :class="[
                        'TARS' == memory.speaker ? 'bg-zinc-700/[0.15]' : '',
                        'my-4 p-4 border rounded-md border-zinc-700',
                      ]"
                    >
                      <div class="flex pr-4">
                        <img
                          class="h-8 w-8 rounded-full mr-3 bg-cover bg-zinc-300"
                          :src="
                            'TARS' == memory.speaker
                              ? '/img/icon-tars.svg'
                              : '/img/icon-helmet.svg'
                          "
                          alt=""
                        />
                        <div class="flex-1">
                          <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-white">
                              {{ memory.speaker }}
                            </h4>
                          </div>
                          <p class="mb-2">{{ memory.message }}</p>
                          <p class="text-sm text-gray-500">{{ memory.date }}</p>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="w-full">
                    <div
                      class="w-full px-2 mx-auto bg-transparent backdrop-blur [@supports(backdrop-filter:blur(0))]:bg-slate-900/25"
                    >
                      <form
                        @submit.prevent="sendMessage"
                        class="flex items-stretch justify-between gap-4"
                      >
                        <Textarea
                          v-model="form.message"
                          :disabled="form.processing"
                          class="block"
                        ></Textarea>
                        <div v-if="form.errors.message">
                          {{ form.errors.message }}
                        </div>
                        <ButtonPrimary
                          :class="'bg-teal-500'"
                          :disabled="form.processing"
                          >Send</ButtonPrimary
                        >
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Spotlight>
</template>
