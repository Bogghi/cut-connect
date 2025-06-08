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
let consoleMounted = false;

app.mount("#nav");
window.history.replaceState({}, '', '/');
router.push('/');

eventBus.on('navigation', destination => {

  switch(destination) {
    case 'console':

      if(!consoleApp) {
        document.querySelectorAll('#landing,#footer')
          .forEach(node => node.classList.add('hidden'));
        consoleApp = initConsole();
        consoleApp.mount("#app");
      }
      router.push('/console/login');

      break;
    case 'home':

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

      if(!consoleApp) {
        document.querySelectorAll('#landing')
          .forEach(node => node.classList.add('hidden'));
        consoleApp = initConsole();
        consoleApp.mount("#app");
      }

      router.push('/console/services');

  }

})