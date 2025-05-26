<script>
import { VueCal } from 'vue-cal'
import 'vue-cal/style'
import BottomSheet from '@douxcode/vue-spring-bottom-sheet'
import '@douxcode/vue-spring-bottom-sheet/dist/style.css'
import { useReservationStore } from "@/console/stores/reservation.store.js";

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
        timeFrom: 8*60,
        timeTo: 20*60,
      },
      events: [
        {
          eventId: 1,
          start: new Date(new Date().setHours(9,0)),
          end: new Date(new Date().setHours(10, 0)),
          title: 'Matteo Borghi',
          content: "Barbiere: <b>Gianny</b>"
        },
      ]
    };
  },
  methods: {
    doubleClick(event) {
      this.$refs['bottomSheet'].open();
    },
    createEvent(event) {
      const newEvent = {
        start: event.event.start,
        end: event.event.end,
        title: 'Nuovo appuntamento',
      };

      this.reservationStore.addReservation(newEvent, res => {
        console.log('Reservation created:', res);
      });
    }
  }
}
</script>

<template>
  <div class="home-container">
    <vue-cal v-bind="timelineConfig"
             :events="events"
             :time-cell-height="80"
             style="--vuecal-height: 100%; --vuecal-primary-color: var(--main-color);--vuecal-border-radius: 0;"
             :editable-events="{drag: true, resize: true, delete: false, create: true}"
             @event-dblclick="doubleClick"
             @event-create="createEvent" />
    <BottomSheet ref="bottomSheet"> Your awesome content </BottomSheet>
  </div>
</template>

<style scoped>
.home-container {
  width: 100vw;
  height: calc(100vh - 70px);
}
</style>