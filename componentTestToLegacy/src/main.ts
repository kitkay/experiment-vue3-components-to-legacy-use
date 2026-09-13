import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import './style.css'

const app = createApp(App, {
  payload: {
    user: {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
    },
    orders: [
      {
        id: 1001,
        product: 'Keyboard',
        quantity: 2,
        price: 99.99,
        status: 'paid',
      },
      {
        id: 1002,
        product: 'Mouse',
        quantity: 1,
        price: 49.99,
        status: 'pending',
      },
    ],
  },
})

app.use(createPinia())
app.use(router)

app.mount('#app')