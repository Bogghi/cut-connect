import { defineStore } from "pinia";
import API from "@/shared/utils/API.js";
import { convertUtcToCet } from "@/shared/utils/helpers-function.js";

export const useReservationStore = defineStore("reservation", {
  state: () => {
    return {
      reservations: [],
      windowType: "day"
    };
  },
  actions: {
    addReservation(reservation, callback) {
      API.init().addReservation({
        reservation,
        callback: res => {
          if (res.status === "OK") {
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    getReservations({ window_type, start, end = null, callback }) {
      API.init().getReservations({
        window_type,
        start,
        end,
        callback: res => {
          if (res.status === "OK") {
            this.reservations = this.normalizeReservations(res.reservations);
            this.windowType = window_type;
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
  }
});