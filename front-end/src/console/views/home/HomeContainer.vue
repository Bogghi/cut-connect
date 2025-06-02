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
    }
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
          this.$refs['bottomSheet'].open(this.reservationStore.getCurrentReservation);
        }
        else {
          alert('Errore durante la creazione della prenotazione');
        }
      });
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
      else if(this.reservationStore.windowType === 'week' && this.windowEnd !== null) {
        params['end'] = this.windowEnd;
      }
      this.reservationStore.getReservations(params);
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
             @event-create="createEvent" />
    <BottomSheet ref="bottomSheet">
      <div class="reservation-info-container">
        <h3>Prenotazione</h3>
        <div class="reservation-details">
          <div class="form-group">
            <label for="barbiere">Barbiere</label>
            <select id="barbiere" name="barbiere">
              <option selected disabled>Seleziona un barbiere</option>
              <option value="mario_rossi">Mario Rossi</option>
              <option value="luca_bianchi">Luca Bianchi</option>
              <option value="paolo_verdi">Paolo Verdi</option>
            </select>
          </div>

          <div class="form-group">
            <label for="data">Data</label>
            <input type="date" id="data" name="data" required>
          </div>

          <div class="form-group">
            <label for="ora_inizio">Ora Inizio</label>
            <input type="time" id="ora_inizio" name="ora_inizio" required>
          </div>

          <div class="form-group">
            <label for="ora_fine">Ora Fine (stimata)</label>
            <input type="time" id="ora_fine" name="ora_fine" required>
          </div>

          <div class="form-group">
            <label for="nome_cliente">Nome del Cliente</label>
            <input type="text" id="nome_cliente" name="nome_cliente" placeholder="Es. Mario Rossi" required>
          </div>

          <div class="form-group">
            <label for="numero_telefono">Numero di Telefono</label>
            <input type="tel" id="numero_telefono" name="numero_telefono" placeholder="Es. 3331234567" pattern="[0-9]{8,10}" title="Inserisci un numero di telefono valido (8-10 cifre)">
          </div>

          <div class="form-group">
            <label for="descrizione">Descrizione (opzionale)</label>
            <textarea id="descrizione" name="descrizione" rows="4" placeholder="Eventuali note o preferenze..."></textarea>
          </div>

        </div>
        <div class="reservation-actions">
          <button class="btn btn-danger" @click="deleteReservation">Cancella</button>
          <button class="btn btn-success">Salva</button>
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

    .form-group {
      display: flex;
      flex-direction: column;

      select {
        border: 1px solid #d1d5db; /* Gray border */
        font-size: 14px;
        border-radius: 5px; /* Rounded corners for inputs */
        display: block;
        width: 100%;
        padding: 0.75rem; /* Padding inside inputs */
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
      }
    }
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