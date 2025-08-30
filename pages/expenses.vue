<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Expense Management</h1>
        <p class="text-gray-600">Track all expenses and outgoing transactions</p>
      </div>
      <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Expense
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Today's Expenses</p>
        <p class="text-2xl font-bold text-red-600">$1,250</p>
        <p class="text-xs text-gray-500">8 transactions</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Week</p>
        <p class="text-2xl font-bold text-gray-900">$8,920</p>
        <p class="text-xs text-red-600">+5% from last week</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Month</p>
        <p class="text-2xl font-bold text-gray-900">$34,567</p>
        <p class="text-xs text-green-600">-3% from last month</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Year</p>
        <p class="text-2xl font-bold text-gray-900">$234,567</p>
        <p class="text-xs text-red-600">+12% YTD</p>
      </div>
    </div>

    <!-- Expenses by Category -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Expenses by Store</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Jakarta</span>
            <span class="font-medium text-red-600">-$12,450</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Bandung</span>
            <span class="font-medium text-red-600">-$9,230</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Store Surabaya</span>
            <span class="font-medium text-red-600">-$7,670</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Main Warehouse</span>
            <span class="font-medium text-red-600">-$5,217</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Expenses by Category</h2>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm">Inventory Purchase</span>
            <span class="font-medium text-red-600">-$18,450</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Salaries</span>
            <span class="font-medium text-red-600">-$8,230</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Rent & Utilities</span>
            <span class="font-medium text-red-600">-$4,670</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Marketing</span>
            <span class="font-medium text-red-600">-$2,100</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Other</span>
            <span class="font-medium text-red-600">-$1,117</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold">Recent Expense Transactions</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store/Dept</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">EXP-2024-001</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Main Warehouse</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Inventory</td>
              <td class="px-6 py-4 text-sm">Purchase Order PO-456</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">-$3,245.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Paid
                </span>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">EXP-2024-002</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Jakarta</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Utilities</td>
              <td class="px-6 py-4 text-sm">Electricity Bill - January</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">-$450.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-14</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">EXP-2024-003</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">All Stores</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Marketing</td>
              <td class="px-6 py-4 text-sm">Social Media Ads Campaign</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">-$850.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Paid
                </span>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-14</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">EXP-2024-004</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Bandung</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Maintenance</td>
              <td class="px-6 py-4 text-sm">AC Repair Service</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">-$125.00</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Paid
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