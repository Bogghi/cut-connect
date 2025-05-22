import { defineStore } from 'pinia';
import API from "@/shared/utils/API.js";

export const useUserStore = defineStore('user', {
  state: () => {
    return {
      user: null
    };
  },
  actions: {
    login(username, password, callback) {
      API.init().login({
        username,
        password,
        callback: res => {

          if (res.status === 'OK') {
            localStorage.setItem('jwt_token', res.token);
            this.user = this.normalizeUser(res.user);
            callback && callback(true);
          }
          else {
            callback && callback(false);
          }

        },
      });
    },
    normalizeUser(user) {
      return {
        'user_id': parseInt(user.user_id),
        'username': user.user_name,
        'email': user.email,
      }
    },
  },
});