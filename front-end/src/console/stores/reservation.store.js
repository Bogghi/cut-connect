import { defineStore } from "pinia";
import API from "@/shared/utils/API.js";
import { convertUtcToCet } from "@/shared/utils/helpers-function.js";

export const useReservationStore = defineStore("reservation", {
  state: () => {
    return {
      reservations: [],
      windowType: "day",
      currentReservationId: null,
      viewWindow: {
        start: null,
        end: null
      }
    };
  },
  actions: {
    addReservation(reservation, callback) {
      API.init().addReservation({
        reservation,
        callback: res => {
          if (res.status === "OK") {
            this.getReservations({
              start: this.viewWindow.start,
              end: this.viewWindow.end,
              callback: getRes => {
                this.currentReservationId = res.res.reservation_id;
                callback && callback(true, getRes);
              }
            });
          } else {
            callback && callback(false);
          }
        }
      });
    },
    getReservations({ start, end = null, callback }) {
      let self = this;
      this.viewWindow.start = start;
      this.viewWindow.end = end || start;
      API.init().getReservations({
        window_type: self.windowType,
        start,
        end,
        callback: res => {
          if (res.status === "OK") {
            this.reservations = this.normalizeReservations(res.reservations);
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    normalizeReservations(reservations) {
      return reservations.map(reservation => {
        const startUtcString = `${reservation.reservation_date}T${reservation.start_time}Z`;
        const endUtcString = `${reservation.reservation_date}T${reservation.end_time}Z`;

        // Convert UTC strings to Date objects
        const startUtcDate = new Date(startUtcString);
        const endUtcDate = new Date(endUtcString);

        // Convert the UTC Date objects to CET
        const startDateObjCET = convertUtcToCet(startUtcDate);
        const endDateObjCET = convertUtcToCet(endUtcDate);
        return {
          ...reservation,
          startDateObj: startDateObjCET,
          endDateObj: endDateObjCET,
        };
      });
    }
  },
  getters: {
    getCurrentReservation(state) {
      return state.reservations.find(reservation => reservation.reservation_id === state.currentReservationId);
    }
  }
});