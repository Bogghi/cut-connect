<script>
export default {
  name: 'ServicesContainer'
}
</script>

<template>
  <div class="services-container">
    <div class="banner">
      <h1>I Nostri servizi</h1>
      <p>Scopri la nostra gamma di servizi di barbiere professionale pensati per aiutarti ad apparire e sentirti al meglio.</p>
    </div>
  </div>
</template>

<style scoped>
.services-container {
  display: flex;
  flex-direction: column;
  align-items: center;

  .banner {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: var(--main-color);
    color: white;
    width: 100%;
    padding-bottom: 30px;

    p {
      inline-size: 750px;
      text-align: center;
    }
  }
}
</style>