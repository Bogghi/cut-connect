<script>
import { useServicesStore } from "@/console/stores/index.js";

export default {
  name: 'ServicesContainer',
  setup() {
    const servicesStore = useServicesStore();

    return {
      servicesStore
    };
  },
  methods: {
    addService() {

    }
  },
  mounted() {
    this.servicesStore.getServices(res => {
      if(!res) {
        alert("Errore durante il caricamento dei servizi");
      }
    });
  }
}
</script>

<template>
  <div class="services-container">
    <div class="banner">
      <h1>I Nostri servizi</h1>
      <p>Scopri la nostra gamma di servizi di barbiere professionale pensati per aiutarti ad apparire e sentirti al meglio.</p>
    </div>

    <div class="services">
      <div class="service add clickable" title="aggiungi servizio" @click="addService">
        <i class="fa-solid fa-plus"></i>
      </div>
    </div>
  </div>
</template>

<style scoped>
.services-container {
  display: flex;
  flex-direction: column;
  align-items: center;

  .banner {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: var(--main-color);
    color: white;
    width: 100%;
    padding-bottom: 30px;

    p {
      inline-size: 750px;
      text-align: center;
    }
  }
}
</style>