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
          <h1 class="text-xl font-semibold text-gray-900">Sales Report</h1>
        </div>
      </div>
    </header>

    <div class="p-4">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">Today's Sales</p>
          <p class="text-2xl font-bold text-gray-900">$2,450</p>
          <p class="text-xs text-green-600">+12% from yesterday</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">This Week</p>
          <p class="text-2xl font-bold text-gray-900">$14,890</p>
          <p class="text-xs text-green-600">+8% from last week</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <p class="text-sm text-gray-500">This Month</p>
          <p class="text-2xl font-bold text-gray-900">$67,234</p>
          <p class="text-xs text-red-600">-3% from last month</p>
        </div>
      </div>

      <!-- Recent Sales Table -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Recent Sales</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm">#1234</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">John Smith</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">3</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$245.00</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Completed
                  </span>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm">#1235</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">Jane Doe</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">1</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$89.99</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Processing
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
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