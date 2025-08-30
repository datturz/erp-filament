<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Income Management</h1>
        <p class="text-gray-600">Track all income transactions across stores</p>
      </div>
      <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Income
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Today's Income</p>
        <p class="text-2xl font-bold text-green-600">$3,450</p>
        <p class="text-xs text-gray-500">12 transactions</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Week</p>
        <p class="text-2xl font-bold text-gray-900">$18,920</p>
        <p class="text-xs text-green-600">+15% from last week</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Month</p>
        <p class="text-2xl font-bold text-gray-900">$67,450</p>
        <p class="text-xs text-green-600">+8% from last month</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Year</p>
        <p class="text-2xl font-bold text-gray-900">$456,789</p>
        <p class="text-xs text-green-600">+22% YTD</p>
      </div>
    </div>

    <!-- Income by Store -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Income by Store</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Jakarta</span>
            <span class="font-medium">$28,450</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Bandung</span>
            <span class="font-medium">$19,230</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Surabaya</span>
            <span class="font-medium">$15,670</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Online Sales</span>
            <span class="font-medium">$4,100</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Income by Category</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm">Product Sales</span>
            <span class="font-medium">$52,450</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Services</span>
            <span class="font-medium">$8,230</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Shipping Fees</span>
            <span class="font-medium">$3,670</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Other</span>
            <span class="font-medium">$3,100</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Recent Income Transactions</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">INC-2024-001</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Jakarta</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Product Sales</td>
              <td class="px-6 py-4 text-sm">Sale - Order #1234</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">+$245.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Completed
                </span>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">INC-2024-002</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Bandung</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Product Sales</td>
              <td class="px-6 py-4 text-sm">Sale - Order #1235</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">+$189.50</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Completed
                </span>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">INC-2024-003</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Online</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Shipping</td>
              <td class="px-6 py-4 text-sm">Shipping fee - Order #1236</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">+$15.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: 'auth'
})
</script>