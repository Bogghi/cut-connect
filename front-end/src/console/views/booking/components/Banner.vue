<script>
export default {
  name: 'Banner',
  props:{
    title: {
      type: String,
      default: ''
    },
    subTitle: {
      type: String,
      default: ''
    }
  }
}
</script>

<template>
  <div class="banner">
    <h1>{{ title }}</h1>
    <span v-html="subTitle"></span>
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
</style>