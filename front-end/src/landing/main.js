import AppNavigator from '@/shared/components/AppNavBar.vue'
import '@/shared/assets/css/global.css';

import {createApp} from 'vue';

const app = createApp(AppNavigator);
app.mount("#nav");