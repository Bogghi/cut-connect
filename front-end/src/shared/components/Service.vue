<script>
import { readablePrice } from "@/shared/utils/helpers-function.js";

export default {
  name: 'Service',
  emits: ['editService', 'reserveService'],
  props: {
    serviceId: {
      type: Number,
    },
    serviceName: {
      type: String,
    },
    description: {
      type: String,
      default: ''
    },
    duration: {
      type: Number,
    },
    price: {
      type: Number,
      default: 0,
    },
    editable: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    formattedPrice() {
      return readablePrice(this.price);
    }
  }
}
</script>

<template>
  <div class="service" :key="serviceId">
    <h3>{{ serviceName }}</h3>
    <p class="txt-2">{{ description }}</p>
    <div class="service-time">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
           stroke-linejoin="round" class="lucide lucide-clock h-3 w-3 mr-1">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 16 14"></polyline>
      </svg>
      <span class="txt-2">{{ duration }} minuti</span>
    </div>
    <h3 class="txt-secondary">{{ formattedPrice }}€</h3>

    <button class="btn btn-secondary" @click="$emit('reserveService', serviceId)">Prenota</button>
    <button class="btn" v-if="editable" @click="$emit('editService', serviceId)">
      <i class="fa-solid fa-pen"></i>
    </button>
  </div>
</template>

<style scoped>
.service {
  border: 1px solid #E2E8F0;
  border-radius: 10px;
  box-shadow: 0 0 10px #E2E8F0;
  padding: 30px;
  width: 200px;

  h3 {
    margin-bottom: 0;
  }
  p {
    margin: 0 0 10px 0;
    font-size: 14px;
  }

  .service-time {
    display: flex;
    align-items: center;
    gap: 5px;
    svg {
      width: 13px;
      color: #7B889C;
    }
    span {
      font-size: 13px;
    }
  }
}
</style>