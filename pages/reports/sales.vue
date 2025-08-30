<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center">
          <NuxtLink to="/dashboard" class="mr-4">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </NuxtLink>
          <h1 class="text-xl font-semibold text-gray-900">Sales Analytics</h1>
        </div>
      </div>
    </header>

    <div class="p-4">
      <!-- Date Range Selector -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex space-x-4">
          <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Today</button>
          <button class="px-4 py-2 border border-gray-300 rounded-md">This Week</button>
          <button class="px-4 py-2 border border-gray-300 rounded-md">This Month</button>
          <button class="px-4 py-2 border border-gray-300 rounded-md">Custom Range</button>
        </div>
      </div>

      <!-- Sales Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Total Revenue</p>
          <p class="text-2xl font-bold text-gray-900">$67,234</p>
          <p class="text-xs text-green-600">+12.5% from last period</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Orders</p>
          <p class="text-2xl font-bold text-gray-900">342</p>
          <p class="text-xs text-green-600">+8% from last period</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Average Order</p>
          <p class="text-2xl font-bold text-gray-900">$196.60</p>
          <p class="text-xs text-green-600">+4% from last period</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Conversion Rate</p>
          <p class="text-2xl font-bold text-gray-900">3.2%</p>
          <p class="text-xs text-red-600">-0.5% from last period</p>
        </div>
      </div>

      <!-- Top Products -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Top Selling Products</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <div>
              <p class="font-medium">Classic Jeans</p>
              <p class="text-sm text-gray-500">SKU-001</p>
            </div>
            <div class="text-right">
              <p class="font-medium">145 units</p>
              <p class="text-sm text-gray-500">$14,500</p>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <div>
              <p class="font-medium">Slim Fit Chinos</p>
              <p class="text-sm text-gray-500">SKU-002</p>
            </div>
            <div class="text-right">
              <p class="font-medium">89 units</p>
              <p class="text-sm text-gray-500">$8,011</p>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <div>
              <p class="font-medium">Cargo Pants</p>
              <p class="text-sm text-gray-500">SKU-003</p>
            </div>
            <div class="text-right">
              <p class="font-medium">67 units</p>
              <p class="text-sm text-gray-500">$5,360</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false,
  middleware: 'auth'
})
</script>