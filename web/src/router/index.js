import { createRouter, createWebHistory } from 'vue-router'
import ChatView from '@v/ChatView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: ChatView
    }
  ]
})

export default router
