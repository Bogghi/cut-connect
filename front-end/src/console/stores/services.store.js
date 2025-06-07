import { defineStore } from "pinia";
import API from "@/shared/utils/API.js";
import { readablePrice } from "@/shared/utils/helpers-function.js";

export const useServicesStore = defineStore("services", {
  state: () => {
    return {
      services: null,
      currentServiceId: null,
    }
  },
  getters: {
    getServiceById: (state) => {
      return state.services && state.currentServiceId ?
        state.services.find(service => service.service_id === state.currentServiceId) : null;
    },
  },
  actions: {
    getServices(callback) {
      API.init().getServices({
        callback: res => {
          if (res.status === "OK") {
            this.services = this.normalizeServices(res.services);
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    addService(service, callback) {
      API.init().addService({
        service,
        callback: res => {
          if (res.status === "OK") {
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    updateService(service, callback) {
      let data = {
        ...service,
        service_id: this.currentServiceId
      };
      API.init().updateService({
        service: data,
        callback: res => {
          if (res.status === "OK") {
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    deleteService(serviceId, callback) {
      API.init().deleteService({
        serviceId,
        callback: res => {
          if (res.status === "OK") {
            this.services = this.services.filter(s => s.id !== serviceId);
            callback && callback(true);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    normalizeServices(services) {
      return services.map(service => {
        return {
          ...service,
          readablePrice: readablePrice(service.price),
        };
      });
    }
  }
});