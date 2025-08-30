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
          <h1 class="text-xl font-semibold text-gray-900">Production Batches</h1>
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          New Batch
        </button>
      </div>
    </header>

    <div class="p-4">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Active Production Batches</h2>
        
        <div class="space-y-4">
          <!-- Batch Card 1 -->
          <div class="border rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">BATCH-789</h3>
                <p class="text-sm text-gray-500">Classic Jeans - Size M</p>
                <p class="text-xs text-gray-400 mt-1">Started: 2024-01-15 09:00</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                In Progress
              </span>
            </div>
            <div class="mt-3">
              <div class="flex justify-between text-sm mb-1">
                <span>Progress</span>
                <span>65%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">65 of 100 units completed</p>
            </div>
          </div>

          <!-- Batch Card 2 -->
          <div class="border rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">BATCH-790</h3>
                <p class="text-sm text-gray-500">Slim Fit Chinos - Size L</p>
                <p class="text-xs text-gray-400 mt-1">Started: 2024-01-15 14:00</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Pending QC
              </span>
            </div>
            <div class="mt-3">
              <div class="flex justify-between text-sm mb-1">
                <span>Progress</span>
                <span>100%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">50 of 50 units completed - Awaiting quality check</p>
            </div>
          </div>

          <!-- Batch Card 3 -->
          <div class="border rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">BATCH-791</h3>
                <p class="text-sm text-gray-500">Cargo Pants - Size S</p>
                <p class="text-xs text-gray-400 mt-1">Scheduled: 2024-01-16 08:00</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                Scheduled
              </span>
            </div>
            <div class="mt-3">
              <p class="text-sm text-gray-600">Target: 75 units</p>
              <p class="text-xs text-gray-500 mt-1">Materials ready, awaiting production start</p>
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