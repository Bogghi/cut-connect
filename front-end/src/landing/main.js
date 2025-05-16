import AppNavigator from '@/shared/components/AppNavBar.vue'
import '@/shared/assets/css/global.css';
import '@/shared/assets/css/landing.css'
import '@/shared/assets/css/footer.css'

import {createApp} from 'vue';

const app = createApp(AppNavigator);
app.mount("#nav");