<script>
import Banner from '@/console/views/booking/components/Banner.vue';
import { useServicesStore, useUsersStore } from "@/console/stores/index.js";
import router from "@/console/router/index.js";

export default {
  name: 'SecondStep',
  setup() {
    const serviceStore = useServicesStore();
    const usersStore = useUsersStore();

    return {
      serviceStore,
      usersStore,
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
      
      return "Hai selto il servizio <b>" + this.serviceStore.getServiceById.service_name + "</b><br>" +
             "Durata: " + this.serviceStore.getServiceById.duration + " minuti<br>" +
             "Prezzo: " + this.serviceStore.getServiceById.readablePrice + "€";
    },
    barbers() {
      if(!this.usersStore.users) {
        return [];
      }
      return this.usersStore.users;
    }
  },
  methods: {
    goToThirdStep(barberId) {
      router.push({ name: 'third-step', params: { barberId: barberId, serviceId: this.serviceStore.currentServiceId } });
    }
  },
  mounted() {
    this.serviceStore.currentServiceId = parseInt(this.$route.params.serviceId) ?? 0;
    this.serviceStore.getServices(res => {
      if(!res) {
        alert("Errore durante il caricamento dei servizi");
      }
    });
    this.usersStore.loadUserInfo(res => {
      if(!res) {
        alert("Errore durante il caricamento delle informazioni utente");
      }
    })
  }
}
</script>

<template>
  <Banner
    title="Scegli il barbiere"
    :sub-title="subTitle" />
  <div class="barbers-container">
    <div class="barber" v-for="barber in barbers">
      <h2>{{barber.username}}</h2>
      <button class="btn btn-primary" @click="goToThirdStep(barber.user_id)">Seleziona</button>
    </div>
  </div>
</template>

<style scoped>
.barbers-container {
  margin-top: 60px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 30px;

  .barber {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width: 300px;
    height: 300px;
    box-shadow: 0 0 10px #E2E8F0;
    border-radius: 10px;
    border: 1px solid grey;
  }
}
</style>