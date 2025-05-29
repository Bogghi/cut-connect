<script>
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { useReservationStore } from "@/console/stores/reservation.store.js";
import { getUTCTimeString, getUTCDateString } from "@/shared/utils/helpers-function.js";

export default {
  setup() {
    const reservationStore = useReservationStore();
    return { reservationStore }
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
    createEvent(eventObj) {
      const newEvent = {
        reservation_date: getUTCDateString(eventObj.event.start),
        start: getUTCTimeString(eventObj.event.start),
        end: getUTCTimeString(eventObj.event.end),
        title: 'Nuovo appuntamento',
      };

      this.reservationStore.addReservation(newEvent, (res, reservationId) => {
        if(res){
          this.reservationStore.currentReservationId = reservationId;
          this.refresh(null, res => {
            if(res) {
              this.$refs['bottomSheet'].open(this.reservationStore.getCurrentReservation);
            }
            else {
              alert('Errore durante la creazione della prenotazione');
            }
          })
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
    this.refresh()
  }
}
</script>

<template>
  <div class="home-container">
    <vue-cal v-bind="timelineConfig"
             :events="reservations"
             :time-cell-height="80"
             style="--vuecal-height: 100%; --vuecal-primary-color: var(--main-color);--vuecal-border-radius: 0;"
             :editable-events="{drag: true, resize: true, delete: false, create: true}"
             @event-dblclick="e => doubleClick(e.event)"
             @event-create="createEvent" />
    <BottomSheet ref="bottomSheet">
      <div class="reservation-info-container">
        <h3>Dettagli prenotazione</h3>
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