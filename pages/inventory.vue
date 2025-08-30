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
          <h1 class="text-xl font-semibold text-gray-900">Inventory Management</h1>
        </div>
      </div>
    </header>

    <div class="p-4">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Current Inventory</h2>
        
        <!-- Search Bar -->
        <div class="mb-4">
          <input 
            type="text" 
            placeholder="Search products..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Inventory Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">SKU-001</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Classic Jeans</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Denim</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">245</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    In Stock
                  </span>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">SKU-002</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Slim Fit Chinos</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Casual</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">89</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    In Stock
                  </span>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">SKU-003</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cargo Pants</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Utility</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Low Stock
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