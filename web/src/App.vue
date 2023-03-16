<script setup>
import ChatView from '@v/ChatView.vue'
</script>

<template>
  <ChatView />
</template>

<style scoped>
pre {
  line-height: 1;
  white-space: break-spaces;
  padding: 20px;
  margin: 20px 0;
  background: rgba(24, 24, 27, 0.55);
  border-radius: 0.375rem;
  border: 1px solid rgba(255, 255, 255, 0.15);
}
pre code[class*='language-'] {
  font-size: 13px;
  color: #ddd;
}

.chat_list__item:last-child {
  margin-bottom: 0;
}
.chat_list__item ul {
  list-style: disc;
  margin: 10px 0 10px 1.5rem;
}
.chat_list__item li {
  padding: 3px;
}
</style>
