import { defineStore } from 'pinia';
import API from "@/shared/utils/API.js";

export const useReservationStore = defineStore('reservation', {
  state: () => {
    return {
      reservations: [],
    };
  },
  actions: {
    addReservation(reservation, callback) {
      API.init().addReservation({
        reservation,
        callback: res => {
          if (res.status === 'OK') {
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        },
      });
    }
  }
});