import { createRouter, createWebHistory } from "vue-router";
import Login from "@/console/views/login/Login.vue";
import Home from "@/console/views/home/HomeContainer.vue";
import ServicesContainer from "@/console/views/services/ServicesContainer.vue";

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
    },
    {
      path: '/console/services',
      component: ServicesContainer,
    }
  ]
});

export default router;