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
          <h1 class="text-xl font-semibold text-gray-900">Cycle Count</h1>
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Start New Count
        </button>
      </div>
    </header>

    <div class="p-4">
      <!-- Summary Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Accuracy Rate</p>
          <p class="text-2xl font-bold text-gray-900">98.5%</p>
          <p class="text-xs text-green-600">Above target</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Items Counted Today</p>
          <p class="text-2xl font-bold text-gray-900">145</p>
          <p class="text-xs text-gray-600">15 remaining</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Discrepancies</p>
          <p class="text-2xl font-bold text-gray-900">3</p>
          <p class="text-xs text-yellow-600">Needs review</p>
        </div>
      </div>

      <!-- Count Schedule -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Today's Count Schedule</h2>
        
        <div class="space-y-3">
          <div class="border rounded-lg p-4 bg-green-50 border-green-200">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">Zone A - Denim Section</h3>
                <p class="text-sm text-gray-600 mt-1">SKU-001 to SKU-025</p>
                <p class="text-xs text-gray-500 mt-1">Counted by: John Smith at 09:30</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                Completed
              </span>
            </div>
            <div class="mt-2 text-sm">
              <span class="text-green-600">✓ No discrepancies found</span>
            </div>
          </div>

          <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-200">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">Zone B - Casual Wear</h3>
                <p class="text-sm text-gray-600 mt-1">SKU-026 to SKU-050</p>
                <p class="text-xs text-gray-500 mt-1">In progress since: 11:45</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                In Progress
              </span>
            </div>
            <div class="mt-2 text-sm">
              <span class="text-yellow-600">⚠ 2 discrepancies found - under review</span>
            </div>
          </div>

          <div class="border rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">Zone C - Utility Pants</h3>
                <p class="text-sm text-gray-600 mt-1">SKU-051 to SKU-075</p>
                <p class="text-xs text-gray-500 mt-1">Scheduled: 14:00</p>
              </div>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                Pending
              </span>
            </div>
          </div>
        </div>

        <!-- Discrepancy Report -->
        <div class="mt-6 pt-6 border-t">
          <h3 class="font-medium text-gray-900 mb-3">Discrepancy Report</h3>
          <div class="space-y-2">
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600">SKU-032 - Slim Fit Chinos (M)</span>
              <span class="text-red-600">Expected: 45 | Actual: 43 (-2)</span>
            </div>
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600">SKU-041 - Relaxed Jeans (L)</span>
              <span class="text-red-600">Expected: 30 | Actual: 28 (-2)</span>
            </div>
            <div class="flex justify-between items-center text-sm">
              <span class="text-gray-600">SKU-003 - Cargo Pants (S)</span>
              <span class="text-green-600">Expected: 12 | Actual: 13 (+1)</span>
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