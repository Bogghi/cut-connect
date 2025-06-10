<script>
import Banner from '@/console/views/booking/components/Banner.vue';
import { useServicesStore } from "@/console/stores/index.js";

export default {
  name: 'SecondStep',
  setup() {
    const serviceStore = useServicesStore();

    return {
      serviceStore
    };
  },
  data() {
    return {
      serviceId: null,
    };
  },
  components: {
    Banner,
  },
  computed: {
    subTitle() {
      if(!this.serviceStore.services || !this.serviceStore.currentServiceId) {
        return '';
      }

      console.log(this.serviceStore.currentServiceId);
      return "Hai selto il servizio <b>" + this.serviceStore.getServiceById.service_name + "</b><br>" +
             "Durata: " + this.serviceStore.getServiceById.duration + " minuti<br>" +
             "Prezzo: " + this.serviceStore.getServiceById.readablePrice + "€";
    }
  },
  mounted() {
    this.serviceStore.currentServiceId = parseInt(this.$route.params.serviceId) ?? 0;
    this.serviceStore.getServices(res => {
      if(!res) {
        alert("Errore durante il caricamento dei servizi");
      }
    });
  }
}
</script>

<template>
  <Banner
    title="Scegli il barbiere"
    :sub-title="subTitle" />
</template>

<style scoped>

</style>