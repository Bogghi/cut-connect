<script>
import { useServicesStore, useUsersStore } from "@/console/stores/index.js";
import BottomSheet from "@douxcode/vue-spring-bottom-sheet";
import Service from "@/shared/components/Service.vue";

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
    BottomSheet,
    Service,
  },
  computed: {
    serviceFormSuccessBtnTitle() {
      return this.editing ? 'Modifica' : 'Aggiungi';
    },
    serviceFormDangerBtnTitle() {
      return this.editing ? 'Cancella' : 'Annulla';
    },
    bottomSheetTitle() {
      return this.editing ? "Dettagli servizio" : "Dettagli nuovo servizio";
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
    updateService(serviceData) {
      this.servicesStore.updateService(
        serviceData,
        res => {
          if(res) {
            this.editing = false;
            this.servicesStore.currentServiceId = null;
            this.$refs['service-form'].close();
            this.refresh();
          }
          else {
            alert("Errore durante l'aggiornamento del servizio");
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
        this.updateService(serviceData);
      }
      else {
        this.addService(serviceData);
      }
    },
    abortAction() {
      if(this.editing) {
        this.servicesStore.deleteService(
          this.servicesStore.currentServiceId,
          res => {
            if(res) {
              this.editing = false;
              this.servicesStore.currentServiceId = null;
              this.$refs['service-form'].close();
              this.refresh();
            }
            else {
              alert("Errore durante la cancellazione del servizio");
            }
          }
        );
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

    <div class="services-wrapper">
      <div class="services">
        <Service v-for="service in servicesStore.services"
          :service-id="service.service_id"
          :service-name="service.service_name"
          :description="service.description"
          :duration="parseInt(service.duration)"
          :price="parseInt(service.price)"
          @edit-service="editServiceForm" />
        <div class="service add clickable" title="aggiungi servizio" @click="openAddServiceForm"
             v-if="usersStore.isLoggedIn">
          <i class="fa-solid fa-plus"></i>
        </div>
      </div>
    </div>

    <BottomSheet ref="service-form" @closed="editing = false">
      <h2>{{ bottomSheetTitle }}</h2>
      <div class="service-definition-form">
        <div class="form-group">
          <label for="service-name">Nome del servizio</label>
          <input type="text" id="service_name" placeholder="Es. Taglio capelli"
                 :value="editing ? servicesStore.getServiceById.service_name : ''">
        </div>
        <div class="form-group">
          <label for="service-description">Descrizione</label>
          <input type="text" id="description" placeholder="Es. Taglio capelli con shampoo e asciugatura"
                 :value="editing ? servicesStore.getServiceById.description : ''">
        </div>
        <div class="form-group">
          <label for="service-price">Prezzo <small>(€)</small></label>
          <input type="number" id="price" placeholder="Es. 20.00" step="0.01"
                 :value="editing ? servicesStore.getServiceById.readablePrice : ''">
        </div>
        <div class="form-group">
          <label for="service-duration">Durata <small>(in minuti)</small></label>
          <input type="number" id="duration" placeholder="Es. 30" step="1"
                 :value="editing ? servicesStore.getServiceById.duration : ''">
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
    display: flex;
    width: 1200px;
    margin-bottom: 80px;
    min-height: calc(100vh - 492px);
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