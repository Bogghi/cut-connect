import { createRouter, createWebHistory } from "vue-router";
import Login from "@/console/views/login/Login.vue";
import Home from "@/console/views/home/HomeContainer.vue";
import ServicesContainer from "@/console/views/services/ServicesContainer.vue";
import Booking from "@/console/views/booking/Booking.vue";
import { FirstStep } from "@/console/views/booking/steps/index.js";

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
    },
    {
      path: '/console/booking',
      component: Booking,
      children: [
        {
          path: '',
          name: 'first-step',
          component: FirstStep,
        }
      ]
    }
  ]
});

export default router;