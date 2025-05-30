import { defineStore } from 'pinia';
import API from "@/shared/utils/API.js";

export const useUsersStore = defineStore('user', {
  state: () => {
    return {
      users: null,
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
            localStorage.setItem('refresh_token', res.refreshToken);
            localStorage.setItem('logged_user_id', res.user_id);
            callback && callback(true);
          }
          else {
            callback && callback(false);
          }

        },
      });
    },
    loadUserInfo(callback) {
      const jwtToken = localStorage.getItem('jwt_token');

      if(!jwtToken){
        callback(false);
        return;
      }

      API.init().loadUserInfo({
        token: jwtToken,
        callback: res => {
          if(res.status === 'OK'){
            this.users = this.normalizeUsers(res.users);
            callback && callback(true);
          }
          else {
            callback && callback(false);
          }
        }
      })
    },
    normalizeUsers(users) {
      return users.map((user) => {
        return {
          'user_id': parseInt(user.user_id),
          'username': user.user_name,
          'email': user.email,
        }
      });
    },
    logout() {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('logged_user_id');
      localStorage.removeItem('refresh_token');
      this.users = null;
    },
  },
});