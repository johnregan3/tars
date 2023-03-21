<script setup>
import { ref, onMounted, reactive, nextTick } from 'vue'
import axios from 'axios'
import Spotlight from '@v/Spotlight.vue'
import ButtonPrimary from '@c/SpotButtonPrimary.vue'
import Textarea from '@c/SpotTextarea.vue'
import imgTars from '@a/tars.webp'
import imgCooper from '@a/cooper.webp'

let form = reactive({
  user_id: 1,
  content: '',
  errors: false,
  processing: false
})

const messagesList = ref(null)
const messageTextarea = ref(null)
const inputLength = ref(0)
const messages = ref([])

let updateInputLength = (event) => {
  inputLength.value = event.target.value.length
}

onMounted(() => {
  let start = performance.now()
  axios
    .get('/api/messages')
    .then((response) => {
      console.log('App API Initial Response Time: ' + (performance.now() - start) / 1000 + 's')
      messages.value = response.data
      nextTick(() => {
        scrollToBottom()
      })
    })
    .catch((error) => {
      console.log(error)
    })
  messageTextarea.value.focus()
})

let readyForm = () => {
  form.errors = false
  form.processing = false
  scrollToBottom()
}

let scrollToBottom = () => {
  messagesList.value.scrollTop = messagesList.value.scrollHeight
  window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
  messageTextarea.value.focus()
}

let sendMessage = () => {
  if (form.content === '') {
    return
  }
  let start = performance.now()

  form.processing = true

  messages.value.push({
    id: messages.value.length + 1,
    speaker_id: 1,
    speaker_name: 'Cooper',
    content: form.content,
    timetsamp: 'just now',
    isNew: true
  })
  scrollToBottom()

  // Send current message to API for TARS to respond.
  axios
    .post('/api/messages', form)
    .then((response) => {
      if (response.data.length === 1) {
        response.data[0].isNew = true
        messages.value.push(response.data[0])
        form.content = ''
        console.log('GPT Response Time: ' + (performance.now() - start) / 1000 + 's')
      }
      nextTick(() => {
        readyForm()
      })
    })
    .catch(function (error) {
      console.log(error)
      nextTick(() => {
        readyForm()
      })
    })
}
</script>

<template>
  <Spotlight>
    <div class="sm:px-8 mt-8 mb-12">
      <div class="mx-auto max-w-7xl lg:px-8">
        <div class="relative px-4 sm:px-8 lg:px-12">
          <div class="mx-auto max-w-2xl lg:max-w-5xl">
            <div class="grid grid-cols-1 gap-y-16">
              <div class="lg:order-first lg:row-span-2">
                <div class="relative w-11/12 mx-auto mt-6 text-base text-slate-400 rounded-2xl">
                  <ul
                    ref="messagesList"
                    role="list"
                    class="chat_list max-h-[36rem] mb-5 px-2 relative overflow-scroll"
                  >
                    <li
                      v-for="message in messages"
                      :key="message.id"
                      :class="[
                        '2' == message.speaker_id
                          ? 'bg-slate-700/75 border-gray-800/75 '
                          : 'bg-slate-800/75 border-gray-700 ',
                        true == message.isNew ? 'animate-border-highlight-fade' : '',
                        'chat_list__item mb-4 p-4 border rounded-md'
                      ]"
                    >
                      <div class="flex pr-4">
                        <img
                          :class="[
                            '2' == message.speaker_id ? 'bg-slate-200/60' : 'bg-zinc-200/60',
                            'h-10 w-10 rounded-full mr-3 bg-cover'
                          ]"
                          :src="'2' == message.speaker_id ? imgTars : imgCooper"
                          :alt="message.speaker_name"
                        />
                        <div class="flex-1">
                          <div class="flex items-center justify-between mb-2">
                            <h4 class="text-md font-bold text-white mt-2">
                              {{ message.speaker_name }}
                            </h4>
                          </div>
                          <div v-html="message.content" class="mb-2 text-slate-200"></div>
                          <p class="text-sm text-gray-500">{{ message.timestamp }}</p>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="w-full">
                    <div class="w-full px-2 mx-auto bg-transparent backdrop-blur">
                      <form
                        @submit.prevent="sendMessage"
                        class="flex items-stretch justify-between gap-4"
                      >
                        <div class="w-full">
                          <div
                            :class="[
                              form.errors.message ? 'justify-between' : 'justify-end',
                              'flex items-center mb-2'
                            ]"
                          >
                            <span v-if="form.errors.message" class="text-sm text-red-500">
                              {{ form.errors.message }}
                            </span>
                            <span
                              :class="[
                                inputLength > 500 ? 'text-red-500' : 'text-teal-500',
                                'text-sm text-gray-500'
                              ]"
                            >
                              {{ inputLength }} / 500
                            </span>
                          </div>
                          <Textarea
                            ref="messageTextarea"
                            v-model.lazy="form.content"
                            @change="updateInputLength"
                            :disabled="form.processing"
                            class="block w-full"
                          ></Textarea>
                        </div>
                        <div v-if="form.processing" class="flex items-center justify-center">
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
                        <ButtonPrimary v-else :class="'bg-teal-500'" :disabled="form.processing"
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
        <button @click="scrollToBottom" class="w-8 h-8 color-slate-700">&DownArrowBar;</button>
      </div>
    </div>
  </Spotlight>
</template>
