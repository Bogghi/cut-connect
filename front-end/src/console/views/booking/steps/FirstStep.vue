<script>
export default {
  name: 'FirstStep',
}
</script>

<template>
<div>
  <div class="banner">
    <h1>Prenota il tuo appuntamento</h1>
    <p>Prenota il tuo prossimo appuntamento con i nostri barbieri professionisti ed evita l'attesa.</p>
  </div>
  <div class="services-container">

  </div>
</div>
</template>

<style scoped>
.banner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 30%;
  background-color: var(--main-color);
  color: white;
  padding: var(--booking-padding);
  text-align: center;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  gap: 20px;


  h1 {
    font-size: 2rem;
    margin-bottom: 10px;
  }

  p {
    font-size: 1.2rem;
    margin-top: 0;
  }
}

.banner {
  background-color: var(--main-color);
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}
</style>