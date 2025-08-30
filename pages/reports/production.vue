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
          <h1 class="text-xl font-semibold text-gray-900">Production Report</h1>
        </div>
      </div>
    </header>

    <div class="p-4">
      <!-- Production Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Units Produced</p>
          <p class="text-2xl font-bold text-gray-900">3,456</p>
          <p class="text-xs text-green-600">This month</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Efficiency Rate</p>
          <p class="text-2xl font-bold text-gray-900">94.5%</p>
          <p class="text-xs text-green-600">Above target</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Active Batches</p>
          <p class="text-2xl font-bold text-gray-900">8</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Defect Rate</p>
          <p class="text-2xl font-bold text-gray-900">1.2%</p>
          <p class="text-xs text-green-600">Below threshold</p>
        </div>
      </div>

      <!-- Production by Product Type -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Production by Product Type</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Classic Jeans</p>
              <p class="text-sm text-gray-500">45% of total production</p>
            </div>
            <div class="text-right">
              <p class="text-xl font-bold">1,555</p>
              <p class="text-sm text-gray-500">units</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Slim Fit Chinos</p>
              <p class="text-sm text-gray-500">30% of total production</p>
            </div>
            <div class="text-right">
              <p class="text-xl font-bold">1,037</p>
              <p class="text-sm text-gray-500">units</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Cargo Pants</p>
              <p class="text-sm text-gray-500">15% of total production</p>
            </div>
            <div class="text-right">
              <p class="text-xl font-bold">518</p>
              <p class="text-sm text-gray-500">units</p>
            </div>
          </div>
          <div class="flex justify-between items-center p-3 border rounded-lg">
            <div>
              <p class="font-medium">Other</p>
              <p class="text-sm text-gray-500">10% of total production</p>
            </div>
            <div class="text-right">
              <p class="text-xl font-bold">346</p>
              <p class="text-sm text-gray-500">units</p>
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