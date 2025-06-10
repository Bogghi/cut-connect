<script>
import Service from '@/shared/components/Service.vue';
import Banner from '@/console/views/booking/components/Banner.vue';
import { useServicesStore } from "@/console/stores/index.js";
import router from "@/console/router/index.js";

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
    Banner,
  },
  methods: {
    goToSecondStep(serviceId) {
      router.push({ name: 'second-step', params: { serviceId: serviceId } });
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
<div>
  <Banner
    title="Prenota il tuo appuntamento"
    sub-title="Prenota il tuo prossimo appuntamento con i nostri barbieri professionisti ed evita l'attesa."/>
  <div class="services-container">
    <Service v-for="service in servicesStore.services"
             :service-id="service.service_id"
             :service-name="service.service_name"
             :description="service.description"
             :duration="parseInt(service.duration)"
             :price="parseInt(service.price)"
             @reserve-service="goToSecondStep" />
  </div>
</div>
</template>

<style scoped>
.services-container {
  display: flex;
  flex-direction: column;
  padding: var(--booking-padding);
  align-items: center;
  gap: var(--booking-padding);
}
</style>