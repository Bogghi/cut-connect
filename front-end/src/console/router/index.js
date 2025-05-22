import { createRouter, createWebHistory } from "vue-router";
import Login from "@/console/views/login/Login.vue";
import Home from "@/console/views/home/HomeContainer.vue";

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
    },
    {
      path: '/console/home',
      component: Home
    }
  ]
});

export default router;