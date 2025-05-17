<template>
  <nav class="nav-bar">
    <div class="icon" @click="navigateTo('home')">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-scissors h-6 w-6"
      >
        <circle cx="6" cy="6" r="3"></circle>
        <path d="M8.12 8.12 12 12"></path>
        <path d="M20 4 8.12 15.88"></path>
        <circle cx="6" cy="18" r="3"></circle>
        <path d="M14.8 14.8 20 20"></path>
      </svg>
      <span class="icon-text">I Barbieri Lissone</span>
    </div>
    <div class="navigation">
      <div class="nav-link" @click="navigateTo('console')">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="lucide lucide-house mr-1 h-4 w-4"
        >
          <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
          <path
            d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
          ></path>
        </svg>
        <span class="nav-link-label">Console</span>
      </div>
      <div class="nav-link" v-if="currentRoute === 'home'">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="lucide lucide-scissors mr-1 h-4 w-4"
        >
          <circle cx="6" cy="6" r="3"></circle>
          <path d="M8.12 8.12 12 12"></path>
          <path d="M20 4 8.12 15.88"></path>
          <circle cx="6" cy="18" r="3"></circle>
          <path d="M14.8 14.8 20 20"></path>
        </svg>
        <span class="nav-link-label">Servizi</span>
      </div>
    </div>
  </nav>
</template>

<script>
import eventBus from '@/shared/utils/eventBus'

export default {
  name: "AppNavBar",
  data() {
    return {
      currentRoute: 'home'
    }
  },
  methods: {
    navigateTo(app) {
      switch (app){
        case 'console':
          this.currentRoute = 'console';
          eventBus.emit('navigation', 'console');
          break;
        case 'home':
          this.currentRoute = 'home';
          eventBus.emit('navigation', 'home');
          break;
        default:
          console.warn('No app found');
          break;
      }
    }
  }
};
</script>

<style scoped>
.nav-bar {
  display: flex;
  justify-content: flex-start;
  gap: 50px;
  height: 50px;
  background-color: var(--main-color);
  color: white;
  padding: 10px;

  .icon {
    display: flex;
    align-items: center;
    gap: 0.5rem;

    .icon-text {
      font-weight: 700;
      font-size: 1.25rem;
      line-height: 1.75rem;
    }
  }

  .navigation {
    display: flex;
    align-items: center;
    gap: 10px;

    .nav-link {
      cursor: pointer;
      display: flex;
      gap: 5px;
      align-items: center;

      svg {
        height: 18px;
      }

      .nav-link-lable {
        font-size: 18px;
      }
    }
  }
}
</style>
