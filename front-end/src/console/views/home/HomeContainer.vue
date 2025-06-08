<script>
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { useReservationStore, useUsersStore, useServicesStore } from "@/console/stores/index.js";
import {
  getUTCTimeString,
  getUTCDateString,
  getCETDateString,
  readablePrice
} from "@/shared/utils/helpers-function.js";

export default {
  setup() {
    const reservationStore = useReservationStore();
    const userStore = useUsersStore();
    const servicesStore = useServicesStore();
    return { reservationStore, userStore, servicesStore }
  },
  name: 'HomeContainer',
  components: {
    VueCal,
    BottomSheet,
  },
  data() {
    return {
      timelineConfig: {
        time: true,
        views: ['day', 'week'],
        view: 'day',
        timeFrom: 8*60,
        timeTo: 20*60,
        snapToInterval: 30,
        timeCellHeight: 80,
        editableEvents: {drag: true, resize: true, delete: false, create: true}
      },
      windowType: 'day',
      windowStart: getUTCDateString(new Date()),
      windowEnd: null,
      reservationItems: null
    };
  },
  computed: {
    reservations() {
      return this.reservationStore.reservations.map(reservation => {
        const color = reservation.status === 'pending' ? 'rgb(205, 69, 0)' :
          reservation.status === 'confirmed' ? 'var(--main-color)' :
          reservation.status === 'cancelled' ? 'rgb(67, 67, 67)' :
          reservation.status === 'completed' ? 'rgb(39, 42, 0)' : '';
        return {
          reservation_id: reservation.reservation_id,
          start: reservation.startDateObj,
          end: reservation.endDateObj,
          title: reservation.user_name + ' - ' + reservation.client_name,
          content: reservation.description + "<br>" +
            reservation.servicesString + "<br>" +
            readablePrice(reservation.total) + "€<br>",
          backgroundColor: color,
        };
      });
    },
    services() {
      return this.servicesStore.services ?
        this.servicesStore.services.map(service => {
          return {
            name: service.service_name + ' (' + service.readablePrice + '€)',
            service_id: service.service_id,
          }
        }) :
        [];
    },
    getReservationUserId() {
      return this.reservationStore.getCurrentReservation ? this.reservationStore.getCurrentReservation.user_id : 0;
    },
    readableTotal() {
      return this.reservationStore.getCurrentReservation ?
        readablePrice(this.reservationStore.getCurrentReservation.total)+'€' :
        readablePrice(0)+'€';
    }
  },
  methods: {
    doubleClick(event) {
      this.reservationStore.currentReservationId = event.reservation_id;
      this.openReservationBottomSheet();
    },
    createEvent({ event, resolve }) {
      const title = 'Nuovo appuntamento';
      const newEvent = {
        reservation_date: getUTCDateString(event.start),
        start: getUTCTimeString(event.start),
        end: getUTCTimeString(event.end),
        title: title,
      };

      this.reservationStore.addReservation(newEvent, (res, reservationId) => {
        if(res){
          this.reservationStore.currentReservationId = reservationId;
          resolve({
            ...event,
            reservation_id: reservationId,
            title: title
          });

          this.refresh(null, res => {
            if(!res) {
              alert('Errore nel caricamento delle prenotazioni');
            }
            else {
              this.openReservationBottomSheet();
            }
          })
        }
        else {
          alert('Errore durante la creazione della prenotazione');
        }
      });
    },
    saveChanges() {
      const reservationData = {}
      let formNodes = document.querySelectorAll('.form-group>input,.form-group>select,.form-group>textarea');

      for(let i = 0; i < formNodes.length; i++) {
        reservationData[formNodes[i].id] = formNodes[i].value;
      }

      if(this.reservationItems) {
        reservationData['reservation_items'] = this.reservationItems.length > 1 ?
          this.reservationItems.map(item => item.id) :
          [this.reservationItems[0].id];
      }

      this.reservationStore.updateReservation(
        reservationData,
        res => {
          if(res) {
            this.closeReservationBottomSheet();
            this.refresh();
          }
          else {
            alert("Errore durante il salvataggio delle modifiche alla prenotazione");
          }
        }
      );
    },
    deleteReservation() {
      this.reservationStore.deleteReservation(res => {
        if(res.status === 'OK') {
          this.refresh(null, res => {
            if(res) {
              this.closeReservationBottomSheet();
            }
            else {
              alert('Errore nella cancellazione della prenotazione');
            }
          });
        }
        else {
          alert('Errore nella cancellazione della prenotazione');
        }
      });
    },
    refresh(end = null, callback = null) {
      let params = {
        start: this.windowStart,
        windowType: this.windowType,
        callback: !callback ? res => {
          if(!res) {
            alert('Error nel caricamento delle prenotazioni');
          }
        } : callback
      };
      if(end) {
        this.windowEnd = end;
        params['end'] = this.windowEnd;
      }
      else if(this.windowType === 'week' && this.windowEnd !== null) {
        params['end'] = this.windowEnd;
      }
      this.reservationStore.getReservations(params);
    },
    viewChange(view) {

      this.windowStart = view.id === 'week' ? getUTCDateString(view.start) : getCETDateString(view.start);
      this.windowType = view.id;
      let end = getUTCDateString(view.end);
      this.refresh(end, res => {
        if(!res) {
          alert('Errore nel caricamento delle prenotazioni');
        }
      });

    },
    openReservationBottomSheet() {
      this.$refs['bottomSheet'].open();
      this.servicesStore.getServices(res => {
        if(!res) {
          console.error('Errore nel caricamento dei servizi');
        }
        else {
          setTimeout(() => {
            new MultiSelectTag('reservation_items', {
              onChange: res => this.reservationItems = res
            });
          }, 200);
        }
      });
    },
    closeReservationBottomSheet() {
      this.$refs['bottomSheet'].close();
      this.reservationItems = null;
    },
    isServiceSelected(service) {
      return this.reservationStore.getCurrentReservation ?
        this.reservationStore.getCurrentReservation.items.some(item => item.service_id === service.service_id) :
        false;
    },
    paymentAction(method) {

      this.reservationStore.performPayment({
        paymentMethod: method,
        callback: res => {
          if(res) {
            this.refresh();
            this.closeReservationBottomSheet();
          }
          else {
            alert('Errore durante il pagamento della prenotazione');
          }
        }
      });

    }
  },
  mounted() {
    this.refresh();
    this.userStore.loadUserInfo(res => {
      if(!res) {
        console.error('Errore nel caricamento degli utenti');
      }
    });
  }
}
</script>

<template>
  <div class="home-container">
    <vue-cal ref="vue-cal"
             v-bind="timelineConfig"
             :events="reservations"
             style="--vuecal-height: 100%; --vuecal-primary-color: var(--main-color);--vuecal-border-radius: 0;"
             @event-dblclick="e => doubleClick(e.event)"
             @event-create="createEvent"
             @view-change="viewChange"/>
    <BottomSheet ref="bottomSheet">
      <div class="reservation-info-container">
        <h3>Prenotazione</h3>
        <div class="reservation-details">
          <div class="form-group">
            <label for="user_id">Barbiere</label>
            <select id="user_id" name="barbiere">
              <option v-for="user in userStore.users" :value="user.user_id"
                :selected="user.user_id === getReservationUserId">
                {{user.username}}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="status">Stato</label>
            <select id="status">
              <option :selected="reservationStore.getCurrentReservation.status === 'pending'"
                      value="pending">Da confermare</option>
              <option :selected="reservationStore.getCurrentReservation.status === 'confirmed'"
                      value="confirmed">Cofermato</option>
              <option :selected="reservationStore.getCurrentReservation.status === 'cancelled'"
                      value="cancelled">Cancellato</option>
              <option :selected="reservationStore.getCurrentReservation.status === 'completed'"
                      value="completed">Completato</option>
            </select>
          </div>

          <div class="form-group">
            <label for="client_name">Nome del Cliente</label>
            <input type="text" id="client_name" name="client_name" placeholder="Es. Mario Rossi" required
                   :value="reservationStore.getCurrentReservation.client_name">
          </div>

          <div class="form-group">
            <label for="phone_number">Numero di Telefono</label>
            <input type="tel" id="phone_number" name="phone_number" placeholder="Es. 3331234567" pattern="[0-9]{8,10}"
                   title="Inserisci un numero di telefono valido (8-10 cifre)"
                   :value="reservationStore.getCurrentReservation.phone_number">
          </div>

          <div>
            <label for="reservation_items">Servizi scelti</label>
            <select id="reservation_items" multiple>
              <option v-for="service in services" :value="service.service_id" :selected="isServiceSelected(service)">
                {{service.name}}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="reservation_date">Data</label>
            <input type="date" id="reservation_date" name="data" required
                   :value="reservationStore.getCurrentReservation.reservation_date">
          </div>

          <div class="form-group">
            <label for="start_time">Ora Inizio</label>
            <input type="time" id="start_time" name="ora_inizio" required
                   :value="reservationStore.getCurrentReservation.formattedStartTime">
          </div>

          <div class="form-group">
            <label for="end_time">Ora Fine (stimata)</label>
            <input type="time" id="end_time" name="end_time" required
                   :value="reservationStore.getCurrentReservation.formattedEndTime">
          </div>

          <div class="form-group">
            <label for="description">Descrizione (opzionale)</label>
            <textarea id="description" name="description" rows="4" placeholder="Eventuali note o preferenze..."
                      :value="reservationStore.getCurrentReservation.description"></textarea>
          </div>

        </div>
        <div class="reservation-actions">
          <button class="btn btn-danger" @click="deleteReservation">Cancella</button>
          <button class="btn btn-success" @click="saveChanges">Salva</button>
          <b>Totale: {{readableTotal}}</b>
          <button class="btn btn-payment-card" @click="paymentAction('card')">
            <i class="fa-solid fa-credit-card"></i> Carta
          </button>
          <button class="btn btn-payment-cash" @click="paymentAction('cash')">
            <i class="fa-solid fa-money-bill"></i> Contante
          </button>
        </div>
      </div>
    </BottomSheet>
  </div>
</template>

<style scoped>
.reservation-info-container {
  display: flex !important;
  flex-direction: column;
  justify-content: flex-start;

  .reservation-details {
    display: flex;
    flex-direction: column;
  }

  .reservation-actions {
    display: flex;
    gap: 10px;
    align-items: flex-end;

    .btn-payment-card {
      background-color: #007bff;
      color: white;
    }
    .btn-payment-cash {
      background-color: #003d0c;
      color: white;
    }
  }
}

.home-container {
  width: 100vw;
  height: calc(100vh - 70px);
}
</style>