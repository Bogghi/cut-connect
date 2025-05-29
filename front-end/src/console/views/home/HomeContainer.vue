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

      this.reservationStore.addReservation(newEvent, (addRes, refreshRes) => {
        if(addRes && refreshRes){
          this.$refs['bottomSheet'].open(this.reservationStore.getCurrentReservation);
        }
        else {
          alert('Errore durante la creazione della prenotazione');
        }
      });
    }
  },
  mounted() {
    this.reservationStore.getReservations({
      'window_type': this.reservationStore.window_type,
      'start': getUTCDateString(new Date()),
      'callback': res => {
        if(!res) {
          alert('Error nel caricamento delle prenotazioni');
        }
      }
    });
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
      <h3>Dettagli prenotazione</h3>
    </BottomSheet>
  </div>
</template>

<style scoped>
.home-container {
  width: 100vw;
  height: calc(100vh - 70px);
}
</style>