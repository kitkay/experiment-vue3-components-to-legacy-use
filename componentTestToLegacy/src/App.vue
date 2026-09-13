<script setup lang="ts">
import AppHeader from './components/AppHeader.vue'
import SummaryCard from './components/SummaryCard.vue'
import OrderList from './components/OrderList.vue'

import type { AppPayload } from './types/types'

defineProps<{
  payload: AppPayload
}>()
</script>

<template>
  <main>
    <div>
      <AppHeader :user="payload.user" />

      <div>
        <SummaryCard :orders="payload.orders" />

        <OrderList :orders="payload.orders" />
      </div>
    </div>
  </main>
</template>