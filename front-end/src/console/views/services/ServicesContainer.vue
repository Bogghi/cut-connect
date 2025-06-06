<script>
import { useServicesStore, useUsersStore } from "@/console/stores/index.js";
import BottomSheet from "@douxcode/vue-spring-bottom-sheet";

export default {
  name: 'ServicesContainer',
  setup() {
    const servicesStore = useServicesStore();
    const usersStore = useUsersStore();

    return {
      servicesStore,
      usersStore
    };
  },
  data() {
    return {
      editing: false,
    };
  },
  components: {
    BottomSheet
  },
  computed: {
    serviceFormSuccessBtnTitle() {
      return this.editing ? 'Modifica' : 'Aggiungi';
    },
    serviceFormDangerBtnTitle() {
      return this.editing ? 'Cancella' : 'Annulla';
    },
  },
  methods: {
    openAddServiceForm() {
      this.$refs['service-form'].open();
    },
    addService(serviceData) {
      this.servicesStore.addService(
        serviceData,
        res => {
          if(res) {
            this.$refs['service-form'].close();
            this.refresh();
          }
          else {
            alert("Errore durante l'aggiunta del servizio");
          }
        }
      );
    },
    editServiceForm(serviceId) {
      this.servicesStore.currentServiceId = serviceId;
      this.editing = true;
      this.$refs['service-form'].open();
    },
    refresh() {
      this.servicesStore.getServices(res => {
        if(!res) {
          alert("Errore durante il caricamento dei servizi");
        }
      });
    },
    confirmAction() {
      const serviceData = {}
      let formNodes = document.querySelectorAll('.form-group input');
      for(let i = 0; i < formNodes.length; i++) {
        let key = formNodes[i].id;

        if(key === 'price') {
          serviceData[key] = parseFloat(formNodes[i].value)*100;
        }
        else {
          serviceData[key] = formNodes[i].value;
        }

      }

      if(this.editing) {
        // update action
      }
      else {
        this.addService(serviceData);
      }
    },
    abortAction() {
      const serviceData = {}
      let formNodes = document.querySelectorAll('.form-group input');
      for(let i = 0; i < formNodes.length; i++) {
        let key = formNodes[i].id;

        if(key === 'price') {
          serviceData[key] = parseFloat(formNodes[i].value)*100;
        }
        else {
          serviceData[key] = formNodes[i].value;
        }

      }

      if(this.editing) {
        // delete action
      }
      else {
        this.$refs['service-form'].close();
      }
    },
  },
  mounted() {
    this.refresh();
  }
}
</script>

<template>
  <div class="services-container">
    <div class="banner">
      <h1>I Nostri servizi</h1>
      <p>Scopri la nostra gamma di servizi di barbiere professionale pensati per aiutakrti ad apparire e sentirti al meglio.</p>
    </div>

    <div class="services services-wrapper">
      <div class="service" v-for="service in servicesStore.services" :key="service.service_id">
        <h3>{{ service.service_name }}</h3>
        <p class="txt-2">{{ service.description }}</p>
        <div class="service-time">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round" class="lucide lucide-clock h-3 w-3 mr-1">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span class="txt-2">{{ service.duration }} minuti</span>
        </div>
        <h3 class="txt-secondary">{{ service.readablePrice }}€</h3>

        <button class="btn btn-secondary">Prenota</button>
        <button class="btn" @click="editServiceForm(service.service_id)">
          <i class="fa-solid fa-pen"></i>
        </button>
      </div>
      <div class="service add clickable" title="aggiungi servizio" @click="openAddServiceForm"
           v-if="usersStore.isLoggedIn">
        <i class="fa-solid fa-plus"></i>
      </div>
    </div>

    <BottomSheet ref="service-form" @closed="editing = false">
      <h2>Dettagli nuovo servizio</h2>
      <div class="service-definition-form">
        <div class="form-group">
          <label for="service-name">Nome del servizio</label>
          <input type="text" id="service_name" placeholder="Es. Taglio capelli">
        </div>
        <div class="form-group">
          <label for="service-description">Descrizione</label>
          <input type="text" id="description" placeholder="Es. Taglio capelli con shampoo e asciugatura">
        </div>
        <div class="form-group">
          <label for="service-price">Prezzo <small>(€)</small></label>
          <input type="number" id="price" placeholder="Es. 20.00" step="0.01">
        </div>
        <div class="form-group">
          <label for="service-duration">Durata <small>(in minuti)</small></label>
          <input type="number" id="duration" placeholder="Es. 30" step="1">
        </div>
      </div>
      <div class="flex-row">
        <button class="btn btn-success" @click="confirmAction">{{ serviceFormSuccessBtnTitle }}</button>
        <button class="btn btn-danger" @click="abortAction">{{ serviceFormDangerBtnTitle }}</button>
      </div>
    </BottomSheet>
  </div>
</template>

<style scoped>

.services-container {
  display: flex;
  flex-direction: column;
  align-items: center;

  .services-wrapper {
    width: 1200px;
    margin-bottom: 50px;
  }

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