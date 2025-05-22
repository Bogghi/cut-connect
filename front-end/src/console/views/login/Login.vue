<script>
import { useUserStore } from "@/console/stores/user.store.js";

export default {
  name: 'Login',
  setup() {
    const userStore = useUserStore();
    return {
      userStore
    };
  },
  methods: {
    login() {
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;

      if(username && password){
        this.userStore.login(username, password, (res) => {
          if(res) {

          }
          else {
            alert("errore nel login");
          }
        });
      }
      else {
        alert("Inserisci password e username");
      }
    }
  },
  mounted() {
    if(localStorage.getItem('jwt_token') !== null) {
      this.$router.push('/console/home');
    }
  }
}
</script>

<template>
  <div class="login-container">
    <div class="panel shadow">
      <label for="username">Email</label>
      <input type="text" id="username" name="username" placeholder="Email" />
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" />
      <div>
        <button class="btn btn-secondary" @click="login">
          Accedi
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 70px);

  .panel {
    width: 40vw;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
      width: 100vw;
    }
  }
}
</style>