import { createRouter, createWebHistory } from "vue-router";
import Login from "@/console/views/login/Login.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/console/login',
    },
    {
      path: '/console/login',
      component: Login,
    }
  ]
});

export default router;