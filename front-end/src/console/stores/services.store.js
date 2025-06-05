import { defineStore } from "pinia";
import API from "@/shared/utils/API.js";
import { readablePrice } from "@/shared/utils/helpers-function.js";

export const useServicesStore = defineStore("services", {
  state: () => {
    services: null
  },
  getters: {},
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
    // To review
    addService(service, callback) {
      API.init().addService({
        service,
        callback: res => {
          if (res.status === "OK") {
            this.services.push(res.service);
            callback && callback(true, res.service);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    // To review
    updateService(service, callback) {
      API.init().updateService({
        service,
        callback: res => {
          if (res.status === "OK") {
            const index = this.services.findIndex(s => s.id === service.id);
            if (index !== -1) {
              this.services[index] = res.service;
            }
            callback && callback(true, res.service);
          } else {
            callback && callback(false);
          }
        }
      });
    },
    // To Review
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