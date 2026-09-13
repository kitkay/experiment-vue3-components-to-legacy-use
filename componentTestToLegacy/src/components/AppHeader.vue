<script setup lang="ts">
import type { UserPayload } from '../types/types'

defineProps<{
  user: UserPayload
}>()

defineOptions({
  inheritAttrs: false
})
</script>

<template>
  <header class="flex flex-col gap-4 m-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-sm font-medium text-indigo-600">
        Order Management
      </p>

      <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 example">
        Orders
      </h1>
    </div>

    <div class="flex items-center gap-3 rounded-lg bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
      <div
        class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
      >
        {{ user.name.charAt(0).toUpperCase() }}
      </div>

      <div>
        <p class="text-sm font-semibold text-slate-900">
          {{ user.name }}
        </p>

        <p class="text-xs text-slate-500">
          {{ user.email }}
        </p>
      </div>
    </div>
  </header>
</template>

<style scoped>
  .example {
    color: red;
  }
</style>