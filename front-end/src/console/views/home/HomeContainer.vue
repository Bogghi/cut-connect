<script>
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { useReservationStore, useUsersStore } from "@/console/stores/index.js";
import { getUTCTimeString, getUTCDateString } from "@/shared/utils/helpers-function.js";

export default {
  setup() {
    const reservationStore = useReservationStore();
    const userStore = useUsersStore();
    return { reservationStore, userStore }
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
    };
  },
  computed: {
    reservations() {
      return this.reservationStore.reservations.map(reservation => {
        return {
          reservation_id: reservation.reservation_id,
          start: reservation.startDateObj,
          end: reservation.endDateObj,
          title: reservation.user_name,
          content: reservation.description
        };
      });
    },
  },
  methods: {
    doubleClick(event) {
      this.reservationStore.currentReservationId = event.reservation_id;
      this.$refs['bottomSheet'].open();
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
              this.$refs['bottomSheet'].open(this.reservationStore.getCurrentReservation);
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
      let formNodes = document.querySelectorAll('.form-group input,select,textarea');

      for(let i = 0; i < formNodes.length; i++) {
        reservationData[formNodes[i].id] = formNodes[i].value;
      }

      this.reservationStore.updateReservation(
        reservationData,
        res => {
          if(res) {
            this.$refs['bottomSheet'].close();
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
              this.$refs['bottomSheet'].close();
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

      this.windowStart = getUTCDateString(view.start);
      this.windowType = view.id;
      let end = getUTCDateString(view.end);
      this.refresh(end, res => {
        if(!res) {
          alert('Errore nel caricamento delle prenotazioni');
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
                :selected="user.user_id === reservationStore.getCurrentReservation.user_id">
                {{user.username}}
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

          <div class="form-group">
            <label for="description">Descrizione (opzionale)</label>
            <textarea id="description" name="description" rows="4" placeholder="Eventuali note o preferenze..."
                      :value="reservationStore.getCurrentReservation.description"></textarea>
          </div>

        </div>
        <div class="reservation-actions">
          <button class="btn btn-danger" @click="deleteReservation">Cancella</button>
          <button class="btn btn-success" @click="saveChanges">Salva</button>
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
  }
}

.home-container {
  width: 100vw;
  height: calc(100vh - 70px);
}
</style>