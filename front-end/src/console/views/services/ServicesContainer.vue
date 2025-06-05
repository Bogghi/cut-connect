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
  components: {
    BottomSheet
  },
  methods: {
    openAddServiceForm() {
      this.$refs['add-service-form'].open();
    },
    addService() {
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

      this.servicesStore.addService(
        serviceData,
        res => {
          if(res) {
            this.$refs['add-service-form'].close();
            this.refresh();
          }
          else {
            alert("Errore durante l'aggiunta del servizio");
          }
        }
      );
    },
    refresh() {
      this.servicesStore.getServices(res => {
        if(!res) {
          alert("Errore durante il caricamento dei servizi");
        }
      });
    }
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

    <div class="services">
      <div class="service add clickable" title="aggiungi servizio" @click="openAddServiceForm"
           v-if="usersStore.isLoggedIn">
        <i class="fa-solid fa-plus"></i>
      </div>
    </div>

    <BottomSheet ref="add-service-form">
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
        <button class="btn btn-success" @click="addService">Salva</button>
        <button class="btn btn-danger" @click="$refs['add-service-form'].close()">Annulla</button>
      </div>
    </BottomSheet>
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