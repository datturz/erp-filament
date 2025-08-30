<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Incoming Goods</h1>
        <p class="text-gray-600">Manage goods received from suppliers</p>
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Receive Goods
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Today's Receipts</p>
        <p class="text-2xl font-bold">12</p>
        <p class="text-xs text-gray-500">245 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Pending Inspection</p>
        <p class="text-2xl font-bold text-yellow-600">5</p>
        <p class="text-xs text-gray-500">89 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">This Week</p>
        <p class="text-2xl font-bold">67</p>
        <p class="text-xs text-gray-500">1,234 items</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-sm text-gray-500">Total Value</p>
        <p class="text-2xl font-bold">$45,678</p>
        <p class="text-xs text-green-600">This month</p>
      </div>
    </div>

    <!-- Recent Incoming -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Recent Incoming Goods</h2>
        <div class="flex space-x-2">
          <select class="px-3 py-1 border rounded text-sm">
            <option>All Warehouses</option>
            <option>Main Warehouse</option>
            <option>Store Jakarta</option>
            <option>Store Bandung</option>
          </select>
          <select class="px-3 py-1 border rounded text-sm">
            <option>All Status</option>
            <option>Pending</option>
            <option>Inspected</option>
            <option>Stored</option>
          </select>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">RCV-2024-001</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 09:30</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">PT Textile Indonesia</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">PO-2024-456</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">50 units</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Main Warehouse</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  Pending Inspection
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-blue-600 hover:text-blue-900 mr-3">Inspect</button>
                <button class="text-gray-600 hover:text-gray-900">View</button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">RCV-2024-002</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 11:15</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">CV Garment Sejahtera</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">PO-2024-457</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">120 units</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Store Jakarta</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                  Inspected
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button class="text-green-600 hover:text-green-900 mr-3">Store</button>
                <button class="text-gray-600 hover:text-gray-900">View</button>
              </td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">RCV-2024-003</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">2024-01-15 14:00</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Pants Supplier Co</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">PO-2024-458</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">200 units</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">Main Warehouse</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                  Stored
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