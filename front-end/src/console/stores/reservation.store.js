import { defineStore } from "pinia";
import API from "@/shared/utils/API.js";
import { getUTCDateString, getUTCTimeString } from "@/shared/utils/helpers-function.js";

export const useReservationStore = defineStore("reservation", {
  state: () => {
    return {
      reservations: [],
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
             callback(true, res.reservation_id);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    deleteReservation(callback) {
      if(!this.currentReservationId) {
        callback && callback(false);
        return;
      }

      API.init().deleteReservation({reservationId: this.currentReservationId, callback: callback});
    },
    updateReservation(reservation, callback) {

      let self = this;
      let data = {
        ...reservation,
        reservation_id: self.currentReservationId,
      };
      let startDateDB = new Date(reservation.reservation_date+" " + reservation.start_time);
      let endDateDB = new Date(reservation.reservation_date+" " + reservation.end_time);

      data.reservation_date = getUTCDateString(startDateDB);
      data.start_time = getUTCTimeString(startDateDB);
      data.end_time = getUTCTimeString(endDateDB);

      API.init().updateReservation({
        reservation: data,
        callback: res => {
          callback && callback(res.status === "OK");
        }
      });

    },
    getReservations({ start, end = null, windowType = 'day', callback }) {
      API.init().getReservations({
        window_type: windowType,
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
        const startDateObjCET = new Date(startUtcString);
        const endDateObjCET = new Date(endUtcString);

        const startHour = startDateObjCET.getHours() < 10 ?
          '0' + startDateObjCET.getHours() :
          startDateObjCET.getHours();
        const endHours = endDateObjCET.getHours() < 10 ?
          '0' + endDateObjCET.getHours() :
          endDateObjCET.getHours();
        const startMinutes = startDateObjCET.getMinutes() < 10 ?
          '0' + startDateObjCET.getMinutes() :
          startDateObjCET.getMinutes();
        const endMinutes = endDateObjCET.getMinutes() < 10 ?
          '0' + endDateObjCET.getMinutes() :
          endDateObjCET.getMinutes();

        return {
          ...reservation,
          startDateObj: startDateObjCET,
          endDateObj: endDateObjCET,
          formattedStartTime: startHour+":"+startMinutes,
          formattedEndTime: endHours+":"+endMinutes,
        };
      });
    }
  },
  getters: {
    getCurrentReservation(state) {
      return state.reservations.find(reservation => reservation.reservation_id === parseInt(state.currentReservationId));
    },
  }
});