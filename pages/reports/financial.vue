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
          <h1 class="text-xl font-semibold text-gray-900">Financial Report</h1>
        </div>
      </div>
    </header>

    <div class="p-4">
      <!-- Financial Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Total Revenue</p>
          <p class="text-2xl font-bold text-gray-900">$456,789</p>
          <p class="text-xs text-green-600">+15% YTD</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Gross Profit</p>
          <p class="text-2xl font-bold text-gray-900">$189,234</p>
          <p class="text-xs text-green-600">41.4% margin</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Operating Costs</p>
          <p class="text-2xl font-bold text-gray-900">$123,456</p>
          <p class="text-xs text-yellow-600">+5% YTD</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Net Profit</p>
          <p class="text-2xl font-bold text-gray-900">$65,778</p>
          <p class="text-xs text-green-600">14.4% margin</p>
        </div>
      </div>

      <!-- Revenue Breakdown -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-medium mb-4">Revenue Breakdown</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Retail Sales</span>
            <span class="font-medium">$312,456 (68.4%)</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Wholesale</span>
            <span class="font-medium">$98,765 (21.6%)</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Online Sales</span>
            <span class="font-medium">$45,568 (10.0%)</span>
          </div>
        </div>
      </div>

      <!-- Cost Analysis -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Cost Analysis</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Material Costs</p>
              <p class="text-sm text-gray-500">Raw materials and supplies</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-red-600">-$156,789</p>
              <p class="text-sm text-gray-500">34.3% of revenue</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Labor Costs</p>
              <p class="text-sm text-gray-500">Wages and benefits</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-red-600">-$89,234</p>
              <p class="text-sm text-gray-500">19.5% of revenue</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Operating Expenses</p>
              <p class="text-sm text-gray-500">Rent, utilities, marketing</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-red-600">-$45,532</p>
              <p class="text-sm text-gray-500">10.0% of revenue</p>
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