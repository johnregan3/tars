import { createRouter, createWebHashHistory } from "vue-router";
import HelloWorld from './components/HelloWorld.vue'

const routes = [
  {
    path: "/",
    component: HelloWorld,
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

export default router;
