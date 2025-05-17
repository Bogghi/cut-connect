import AppNavBar from '@/shared/components/AppNavBar.vue'
import '@/shared/assets/css/global.css';
import '@/shared/assets/css/landing.css'
import '@/shared/assets/css/footer.css'

import App from '@/console/App.vue';

import {createApp} from 'vue';
import eventBus from '@/shared/utils/eventBus.js';
import router from "@/console/router/index.js";

const app = createApp(AppNavBar);
app.mount("#nav");

eventBus.on('navigation', destination => {
  switch(destination) {
    case 'console':
      document.querySelectorAll('#landing,#footer')
        .forEach(node => node.classList.add('hidden'));

      const consoleApp = createApp(App);
      consoleApp
        .use(router)
        .mount("#app");
      break;
    case 'home':
      const appElement = document.querySelector('#app');
      if(!!appElement.__vue_app__) {
        appElement.__vue_app__.unmount();
        document.querySelectorAll('#landing,#footer')
          .forEach(node => node.classList.remove('hidden'));
      }
      break;
  }
})