import AppNavBar from '@/shared/components/AppNavBar.vue'
import '@/shared/assets/css/global.css';
import '@/shared/assets/css/landing.css'
import '@/shared/assets/css/footer.css'

import App from '@/console/App.vue';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import eventBus from '@/shared/utils/eventBus.js';
import router from "@/console/router/index.js";

const app = createApp(AppNavBar);
const pinia = createPinia();
const initConsole = () => {
  let consoleApp = createApp(App);
  consoleApp
    .use(pinia)
    .use(router);
  return consoleApp;
};
let consoleApp = null;
let fromBooking = false;

app.mount("#nav");
window.history.replaceState({}, '', '/');

eventBus.on('navigation', destination => {

  switch(destination) {
    case 'console':

      if(fromBooking) {
        document.querySelector('#nav').classList.remove('hidden');
        fromBooking = false;
      }

      if(!consoleApp) {
        document.querySelectorAll('#landing,#footer')
          .forEach(node => node.classList.add('hidden'));
        consoleApp = initConsole();
        consoleApp.mount("#app");
      }
      router.push('/console/login');

      break;
    case 'home':

      if(fromBooking) {
        document.querySelector('#nav').classList.remove('hidden');
        fromBooking = false;
      }

      router.push('/');
      if(consoleApp) {
        consoleApp.unmount();
        consoleApp = null;
        window.history.replaceState({}, '', '/');
      }

      document.querySelectorAll('#landing,#footer')
        .forEach(node => node.classList.remove('hidden'));

      break;
    case 'services':

      if(fromBooking) {
        document.querySelector('#nav').classList.remove('hidden');
        fromBooking = false;
      }

      if(!consoleApp) {
        document.querySelectorAll('#landing')
          .forEach(node => node.classList.add('hidden'));
        consoleApp = initConsole();
        consoleApp.mount("#app");
      }

      router.push('/console/services');
      break;
    case 'booking':
      if(!consoleApp) {
        fromBooking = true;
        document.querySelectorAll('#landing,#footer,#nav')
          .forEach(node => node.classList.add('hidden'));
        consoleApp = initConsole();
        consoleApp.mount("#app");
      }

      router.push('/console/booking');
      break;
  }

})

document.querySelector('#reserve-slot').addEventListener('click', () => {
  eventBus.emit('navigation', 'booking');
})