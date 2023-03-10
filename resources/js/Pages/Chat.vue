<script setup>
import { Link, useForm, router } from "@inertiajs/vue3";
import Spotlight from "@/Layouts/Spotlight.vue";
import ButtonPrimary from "@/Components/Spotlight/ButtonPrimary.vue";
import Textarea from "@/Components/Spotlight/Textarea.vue";
import { ref, onMounted } from "vue";

defineProps({
  data: Object,
});

let form = useForm({
  message: "",
});

const messagesList = ref(null);
const messageBlock = ref(null);

onMounted(() => {
  messagesList.value.scrollTop = messagesList.value.scrollHeight;
  focusMessageBlock();
});

let focusMessageBlock = () => {
  messageBlock.value.focus();
};

let scrollToBottom = () => {
  messagesList.value.scrollTop = messagesList.value.scrollHeight;
  window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
  focusMessageBlock();
};

let sendMessage = () => {
  form.post(route("chat.store"), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ preserveState: true });
      form.reset();
      scrollToBottom();
    },
  });
};


</script>

<template>
  <Spotlight title="Chat">
    <div class="sm:px-8 mt-8 mb-12">
      <div class="mx-auto max-w-7xl lg:px-8">
        <div class="relative px-4 sm:px-8 lg:px-12">
          <div class="mx-auto max-w-2xl lg:max-w-5xl">
            <div class="grid grid-cols-1 gap-y-16">
              <div class="lg:order-first lg:row-span-2">
                <div
                  class="relative w-full mx-auto mt-6 text-base text-slate-400 rounded-2xl"
                >
                  <ul
                    ref="messagesList"
                    role="list"
                    class="chat_list max-h-[36rem] mb-5 px-2 relative overflow-scroll"
                  >
                    <li
                      v-for="memory in data.memories"
                      :key="memory.id"
                      :class="[
                        '2' == memory.speaker.id
                          ? 'bg-slate-700/75 border-gray-800/75 '
                          : 'bg-slate-800/75 border-gray-700 ',
                        'chat_list__item mb-4 p-4 border rounded-md',
                      ]"
                    >
                      <div class="flex pr-4">
                        <img
                          :class="[
                            '2' == memory.speaker.id
                              ? 'bg-slate-200/60'
                              : 'bg-zinc-200/60',
                            'h-10 w-10 rounded-full mr-3 bg-cover',
                          ]"
                          :src="
                            '2' == memory.speaker.id
                              ? '/img/tars.webp'
                              : '/img/cooper.webp'
                          "
                          alt=""
                        />
                        <div class="flex-1">
                          <div class="flex items-center justify-between mb-2">
                            <h4 class="text-md font-bold text-white mt-2">
                              {{ memory.speaker.name }}
                            </h4>
                          </div>
                          <div
                            v-html="memory.message"
                            class="mb-2 text-slate-200"
                          ></div>
                          <p class="text-sm text-gray-500">{{ memory.date }}</p>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="w-full">
                    <div
                      class="w-full px-2 mx-auto bg-transparent backdrop-blur"
                    >
                      <form
                        @submit.prevent="sendMessage"
                        class="flex items-stretch justify-between gap-4"
                      >
                        <div
                          v-show="form.processing"
                          class="flex items-center justify-center"
                        >
                          <div
                            class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"
                            role="status"
                          >
                            <span
                              class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]"
                              >Loading...</span
                            >
                          </div>
                        </div>
                        <Textarea
                          ref="messageBlock"
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
    <div class="sticky w-full flex justify-end bottom-0 pb-3 pr-5 transition">
      <div
        class="w-8 h-8 rounded-full bg-slate-300/50 hover:bg-slate-300/75 transition duration-300"
      >
        <button @click="scrollToBottom" class="w-8 h-8 color-slate-700">
          &DownArrowBar;
        </button>
      </div>
    </div>
  </Spotlight>
</template>
