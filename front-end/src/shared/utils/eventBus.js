import { ref } from 'vue'

export const events = ref(new Map())

export default {
  emit(event, ...args) {
    if (events.value.has(event)) {
      events.value.get(event).forEach(callback => callback(...args))
    }
  },
  on(event, callback) {
    if (!events.value.has(event)) {
      events.value.set(event, [])
    }
    events.value.get(event).push(callback)

    // Return function to remove the listener
    return () => {
      const callbacks = events.value.get(event)
      const index = callbacks.indexOf(callback)
      if (index > -1) callbacks.splice(index, 1)
    }
  }
}