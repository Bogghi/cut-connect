<script>
export default {
  name: 'Booking',
}
</script>

<template>
<div class="container">
  <div class="booking-card panel">
    <router-view/>
  </div>
</div>
</template>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;

  --booking-padding: 20px;

  .booking-card {
    width: 500px;
    height: 1166px;

    &.panel { padding: 0; }

    @media (max-width: 500px) {
      width: 100vw;
      height: 100vh;
    }
  }
}
</style>