<script>
import Service from '@/shared/components/Service.vue';
import { useServicesStore } from "@/console/stores/index.js";

export default {
  name: 'FirstStep',
  setup() {
    const servicesStore = useServicesStore();
    return {
      servicesStore
    };
  },
  components: {
    Service,
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
<div>
  <div class="banner">
    <h1>Prenota il tuo appuntamento</h1>
    <p>Prenota il tuo prossimo appuntamento con i nostri barbieri professionisti ed evita l'attesa.</p>
  </div>
  <div class="services-container">
    <Service v-for="service in servicesStore.services"
             :service-id="service.service_id"
             :service-name="service.service_name"
             :description="service.description"
             :duration="parseInt(service.duration)"
             :price="parseInt(service.price)" />
  </div>
</div>
</template>

<style scoped>
.banner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 30%;
  background-color: var(--main-color);
  color: white;
  padding: var(--booking-padding);
  text-align: center;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  gap: 20px;


  h1 {
    font-size: 2rem;
    margin-bottom: 10px;
  }

  p {
    font-size: 1.2rem;
    margin-top: 0;
  }
}

.services-container {
  display: flex;
  flex-direction: column;
  padding: var(--booking-padding);
  align-items: center;
  gap: var(--booking-padding);
}
</style>