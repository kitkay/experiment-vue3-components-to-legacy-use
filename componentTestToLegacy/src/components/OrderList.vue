<script setup lang="ts">
import type { OrderPayload } from '../types/types'

defineProps<{
  orders: OrderPayload[]
}>()

defineOptions({
  inheritAttrs: false
})

function statusClasses(status: OrderPayload['status']) {
  switch (status) {
    case 'paid':
      return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'

    case 'pending':
      return 'bg-amber-50 text-amber-700 ring-amber-600/20'

    case 'cancelled':
      return 'bg-red-50 text-red-700 ring-red-600/20'
  }
}
</script>

<template>
  <section
    class="overflow-hidden rounded-md m-2 shadow-sm ring-2 ring-slate-200 bg-emerald-900"
  >
    <div class="border-b border-slate-200 px-3 py-1">
      <span class="text-xl font-semibold text-slate-100">
        Orders
      </span>

      <p class="mt-1 text-sm text-slate-300">
        Recent customer orders.
      </p>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-3 py-1 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              ID
            </th>

            <th class="px-3 py-1 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Product
            </th>

            <th class="px-3 py-1 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Quantity
            </th>

            <th class="px-3 py-1 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Price
            </th>

            <th class="px-3 py-1 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Status
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200 bg-white">
          <tr
            v-for="order in orders"
            :key="order.id"
            class="transition hover:bg-slate-50"
          >
            <td class="whitespace-nowrap px-3 py-1 text-sm font-medium text-slate-900">
              #{{ order.id }}
            </td>

            <td class="whitespace-nowrap px-3 py-1 text-sm text-slate-700">
              {{ order.product }}
            </td>

            <td class="whitespace-nowrap px-3 py-1 text-sm text-slate-700">
              {{ order.quantity }}
            </td>

            <td class="whitespace-nowrap px-3 py-1 text-sm text-slate-700">
              ${{ order.price.toFixed(2) }}
            </td>

            <td class="whitespace-nowrap px-3 py-1">
              <span
                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
                :class="statusClasses(order.status)"
              >
                {{ order.status }}
              </span>
            </td>
          </tr>

          <tr v-if="orders.length === 0">
            <td
              colspan="5"
              class="px-6 py-12 text-center text-sm text-slate-500"
            >
              No orders found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>