<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Outgoing Goods</h1>
        <p class="text-gray-600">Manage goods shipments and deliveries</p>
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create Shipment
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Today's Shipments</p>
        <p class="text-2xl font-bold">18</p>
        <p class="text-xs text-gray-500">342 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Ready to Ship</p>
        <p class="text-2xl font-bold text-green-600">8</p>
        <p class="text-xs text-gray-500">156 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">In Transit</p>
        <p class="text-2xl font-bold text-blue-600">24</p>
        <p class="text-xs text-gray-500">489 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Delivered Today</p>
        <p class="text-2xl font-bold">15</p>
        <p class="text-xs text-green-600">100% on time</p>
      </div>
    </div>

    <!-- Recent Outgoing -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Recent Outgoing Shipments</h2>
        <div class="flex space-x-2">
          <select class="px-3 py-1 border rounded text-sm">
            <option>All Locations</option>
            <option>Main Warehouse</option>
            <option>Store Jakarta</option>
            <option>Store Bandung</option>
            <option>Store Surabaya</option>
          </select>
          <select class="px-3 py-1 border rounded text-sm">
            <option>All Status</option>
            <option>Preparing</option>
            <option>Ready</option>
            <option>In Transit</option>
            <option>Delivered</option>
          </select>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipment No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">SHP-2024-001</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 08:00</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Transfer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Main Warehouse</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Jakarta</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">45 units</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                  In Transit
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-blue-600 hover:text-blue-900 mr-3">Track</button>
                <button class="text-gray-600 hover:text-gray-900">View</button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">SHP-2024-002</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 09:30</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Customer Order</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Bandung</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Customer - John Doe</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">3 units</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Ready to Ship
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-green-600 hover:text-green-900 mr-3">Ship</button>
                <button class="text-gray-600 hover:text-gray-900">View</button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">SHP-2024-003</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 10:00</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Transfer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Jakarta</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Surabaya</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">20 units</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Preparing
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-yellow-600 hover:text-yellow-900 mr-3">Ready</button>
                <button class="text-gray-600 hover:text-gray-900">View</button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">SHP-2024-004</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-14 16:00</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Customer Order</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Surabaya</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Customer - Jane Smith</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">5 units</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                  Delivered
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-gray-600 hover:text-gray-900">View</button>
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