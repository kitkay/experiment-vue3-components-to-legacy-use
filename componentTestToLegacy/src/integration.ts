import { createApp } from 'vue'

import AppHeader from './components/AppHeader.vue'
import SummaryCard from './components/SummaryCard.vue'
import OrderList from './components/OrderList.vue'

import './style.css'

declare global {
  interface Window {
    __APP_DATA__: {
      user: {
        id: number
        name: string
        email: string
      }
      orders: {
        id: number
        product: string
        quantity: number
        price: number
        status: 'pending' | 'paid' | 'cancelled'
      }[]
    }
  }
}

const payload = window.__APP_DATA__

const components = {
  'app-header': {
    component: AppHeader,
    props: {
      user: payload.user,
    },
  },

  'summary-card': {
    component: SummaryCard,
    props: {
      orders: payload.orders,
    },
  },

  'order-list': {
    component: OrderList,
    props: {
      orders: payload.orders,
    },
  },
}

for (const [name, config] of Object.entries(components)) {
  document
    .querySelectorAll<HTMLElement>(`[data-role="${name}"]`)
    .forEach((element) => {
      createApp(config.component, config.props).mount(element)
    })
}