<script setup lang="ts">
import { computed } from 'vue'
import type { OrderPayload } from '../types/types'

const props = defineProps<{
  orders: OrderPayload[]
}>()

defineOptions({
  inheritAttrs: false
})

const totalOrders = computed(() => props.orders.length)

const totalAmount = computed(() =>
  props.orders.reduce(
    (total, order) => total + order.price * order.quantity,
    0,
  ),
)

const paidOrders = computed(() =>
  props.orders.filter(order => order.status === 'paid').length,
)
</script>

<template>
  <section class="grid gap-4 m-2 sm:grid-cols-3">
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
      <p class="text-sm mx-auto w-fit font-medium text-slate-500">
        Total Orders
      </p>

      <p class="mx-auto mt-2 w-fit text-3xl font-bold text-slate-900">
        {{ totalOrders }}
      </p>
    </div>

    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
      <p class="text-sm mx-auto w-fit font-medium text-slate-500">
        Paid Orders
      </p>

      <p class="mx-auto mt-2 w-fit text-3xl font-bold text-emerald-600">
        {{ paidOrders }}
      </p>
    </div>

    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
      <p class="text-sm mx-auto w-fit font-medium text-slate-500">
        Total Amount
      </p>

      <p class="mx-auto mt-2 w-fit text-3xl font-bold text-slate-900">
        ${{ totalAmount.toFixed(2) }}
      </p>
    </div>
  </section>
</template>