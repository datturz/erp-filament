<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
      <div class="h-16 px-6 bg-gray-800 flex items-center">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
      </div>
      <nav class="mt-4 px-4">
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Dashboard</NuxtLink>
        <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">POS</NuxtLink>
        <NuxtLink to="/sales-history" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Sales History</NuxtLink>
        <NuxtLink to="/sales-report" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Sales Report</NuxtLink>
        <NuxtLink to="/stock-management" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Stock Management</NuxtLink>
        <NuxtLink to="/stock-report" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Stock Report</NuxtLink>
        <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Products</NuxtLink>
        <NuxtLink to="/categories" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Categories</NuxtLink>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-800">Stock Report</h1>
          </div>
          <div class="flex gap-2">
            <button @click="printReport" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
              Print
            </button>
            <button @click="exportReport" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              Export Excel
            </button>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Report Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <h2 class="text-lg font-semibold mb-4">Report Parameters</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
              <select v-model="reportType" class="w-full px-3 py-2 border rounded-lg">
                <option value="current">Current Stock</option>
                <option value="movement">Stock Movement</option>
                <option value="valuation">Stock Valuation</option>
                <option value="aging">Stock Aging</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="selectedCategory" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Categories</option>
                <option value="Denim">Denim</option>
                <option value="Casual">Casual</option>
                <option value="Formal">Formal</option>
                <option value="Sports">Sports</option>
                <option value="Utility">Utility</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Store/Location</label>
              <select v-model="selectedLocation" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Locations</option>
                <option value="main">Main Store</option>
                <option value="branch1">Branch 1</option>
                <option value="branch2">Branch 2</option>
                <option value="warehouse">Warehouse</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
              <select v-model="stockStatus" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="in-stock">In Stock</option>
                <option value="low-stock">Low Stock</option>
                <option value="out-of-stock">Out of Stock</option>
                <option value="overstocked">Overstocked</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button @click="generateReport" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Generate Report
            </button>
          </div>
        </div>

        <!-- Stock Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total SKUs</p>
            <p class="text-3xl font-bold text-gray-900">{{ totalSKUs }}</p>
            <p class="text-xs text-gray-500 mt-2">Active Products</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Stock Value</p>
            <p class="text-3xl font-bold text-gray-900">${{ totalStockValue.toLocaleString() }}</p>
            <p class="text-xs text-green-600 mt-2">+8.5% from last month</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Low Stock Items</p>
            <p class="text-3xl font-bold text-orange-600">{{ lowStockCount }}</p>
            <p class="text-xs text-orange-600 mt-2">Need reorder</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Out of Stock</p>
            <p class="text-3xl font-bold text-red-600">{{ outOfStockCount }}</p>
            <p class="text-xs text-red-600 mt-2">Critical</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Overstocked</p>
            <p class="text-3xl font-bold text-blue-600">{{ overstockedCount }}</p>
            <p class="text-xs text-blue-600 mt-2">Above max level</p>
          </div>
        </div>

        <!-- Stock Valuation by Category -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <h3 class="text-lg font-semibold mb-4">Stock Valuation by Category</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <table class="w-full">
                <thead class="border-b">
                  <tr>
                    <th class="text-left py-2 text-sm">Category</th>
                    <th class="text-right py-2 text-sm">Items</th>
                    <th class="text-right py-2 text-sm">Value</th>
                    <th class="text-right py-2 text-sm">%</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="cat in categoryValuation" :key="cat.name" class="border-b">
                    <td class="py-2 text-sm">{{ cat.name }}</td>
                    <td class="py-2 text-sm text-right">{{ cat.items }}</td>
                    <td class="py-2 text-sm text-right font-medium">${{ cat.value.toLocaleString() }}</td>
                    <td class="py-2 text-sm text-right">
                      <div class="flex items-center justify-end">
                        <div class="w-12 bg-gray-200 rounded-full h-2 mr-2">
                          <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${cat.percentage}%`"></div>
                        </div>
                        <span>{{ cat.percentage }}%</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
              <!-- Pie chart would go here -->
              <p class="text-gray-500">Category Distribution Chart</p>
            </div>
          </div>
        </div>

        <!-- Detailed Stock Report -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Detailed Stock Report</h3>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search products..."
              class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Level</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min/Max</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days in Stock</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in stockItems" :key="item.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ item.sku }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.category }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.location }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center">
                      <span>{{ item.stockLevel }}</span>
                      <div class="w-16 bg-gray-200 rounded-full h-2 ml-2">
                        <div :class="getStockBarClass(item)" class="h-2 rounded-full" :style="`width: ${getStockPercentage(item)}%`"></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.minStock }}/{{ item.maxStock }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">${{ item.unitCost }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${{ (item.stockLevel * item.unitCost).toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(item)" class="px-2 py-1 text-xs rounded-full">
                      {{ getStatusText(item) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.daysInStock }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div class="px-6 py-3 border-t flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing 1 to 10 of {{ totalItems }} results
            </div>
            <div class="flex gap-2">
              <button class="px-3 py-1 border rounded hover:bg-gray-50">Previous</button>
              <button class="px-3 py-1 border rounded hover:bg-gray-50">Next</button>
            </div>
          </div>
        </div>

        <!-- Stock Movement Summary -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Stock Movements</h3>
            <div class="space-y-3">
              <div v-for="movement in recentMovements" :key="movement.id" class="flex items-center justify-between py-2 border-b">
                <div class="flex items-center">
                  <div :class="movement.type === 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'" class="w-8 h-8 rounded-full flex items-center justify-center mr-3">
                    <svg v-if="movement.type === 'in'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium">{{ movement.product }}</p>
                    <p class="text-xs text-gray-500">{{ movement.date }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium">{{ movement.quantity }} units</p>
                  <p class="text-xs text-gray-500">{{ movement.reason }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Stock Alerts</h3>
            <div class="space-y-3">
              <div v-for="alert in stockAlerts" :key="alert.id" class="flex items-start">
                <div :class="getAlertIconClass(alert.type)" class="w-8 h-8 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">{{ alert.title }}</p>
                  <p class="text-xs text-gray-500">{{ alert.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const sidebarOpen = ref(false)
const reportType = ref('current')
const selectedCategory = ref('')
const selectedLocation = ref('')
const stockStatus = ref('')
const searchQuery = ref('')

// Summary data
const totalSKUs = ref(156)
const totalStockValue = ref(125680)
const lowStockCount = ref(12)
const outOfStockCount = ref(3)
const overstockedCount = ref(8)
const totalItems = ref(156)

// Category valuation
const categoryValuation = ref([
  {name: 'Denim', items: 245, value: 45680, percentage: 36},
  {name: 'Casual', items: 189, value: 35240, percentage: 28},
  {name: 'Formal', items: 156, value: 28960, percentage: 23},
  {name: 'Sports', items: 98, value: 10240, percentage: 8},
  {name: 'Utility', items: 67, value: 5560, percentage: 5}
])

// Stock items
const stockItems = ref([
  {id: 1, sku: 'SKU-001', name: 'Classic Jeans', category: 'Denim', location: 'Main Store', stockLevel: 45, minStock: 20, maxStock: 100, unitCost: 45, daysInStock: 15},
  {id: 2, sku: 'SKU-002', name: 'Slim Fit Chinos', category: 'Casual', location: 'Branch 1', stockLevel: 12, minStock: 15, maxStock: 80, unitCost: 35, daysInStock: 28},
  {id: 3, sku: 'SKU-003', name: 'Cargo Pants', category: 'Utility', location: 'Warehouse', stockLevel: 0, minStock: 10, maxStock: 50, unitCost: 40, daysInStock: 45},
  {id: 4, sku: 'SKU-004', name: 'Formal Trousers', category: 'Formal', location: 'Main Store', stockLevel: 67, minStock: 25, maxStock: 75, unitCost: 50, daysInStock: 10},
  {id: 5, sku: 'SKU-005', name: 'Jogger Pants', category: 'Sports', location: 'Branch 2', stockLevel: 120, minStock: 20, maxStock: 60, unitCost: 30, daysInStock: 5}
])

// Recent movements
const recentMovements = ref([
  {id: 1, type: 'in', product: 'Classic Jeans', date: '2024-01-15', quantity: 50, reason: 'Purchase Order'},
  {id: 2, type: 'out', product: 'Slim Fit Chinos', date: '2024-01-15', quantity: 15, reason: 'Sales'},
  {id: 3, type: 'in', product: 'Cargo Pants', date: '2024-01-14', quantity: 30, reason: 'Transfer'},
  {id: 4, type: 'out', product: 'Formal Trousers', date: '2024-01-14', quantity: 8, reason: 'Sales'},
  {id: 5, type: 'out', product: 'Jogger Pants', date: '2024-01-13', quantity: 12, reason: 'Damaged'}
])

// Stock alerts
const stockAlerts = ref([
  {id: 1, type: 'critical', title: 'Out of Stock', description: '3 products are completely out of stock'},
  {id: 2, type: 'warning', title: 'Low Stock Alert', description: '12 products are below minimum stock level'},
  {id: 3, type: 'info', title: 'Overstock Warning', description: '8 products exceed maximum stock level'},
  {id: 4, type: 'warning', title: 'Slow Moving Items', description: '5 products haven\'t sold in 30+ days'}
])

// Methods
const getStockPercentage = (item) => {
  return Math.min(100, (item.stockLevel / item.maxStock) * 100)
}

const getStockBarClass = (item) => {
  const percentage = getStockPercentage(item)
  if (percentage === 0) return 'bg-red-600'
  if (percentage < 30) return 'bg-orange-600'
  if (percentage > 100) return 'bg-blue-600'
  return 'bg-green-600'
}

const getStatusText = (item) => {
  if (item.stockLevel === 0) return 'Out of Stock'
  if (item.stockLevel < item.minStock) return 'Low Stock'
  if (item.stockLevel > item.maxStock) return 'Overstocked'
  return 'In Stock'
}

const getStatusClass = (item) => {
  if (item.stockLevel === 0) return 'bg-red-100 text-red-700'
  if (item.stockLevel < item.minStock) return 'bg-yellow-100 text-yellow-700'
  if (item.stockLevel > item.maxStock) return 'bg-blue-100 text-blue-700'
  return 'bg-green-100 text-green-700'
}

const getAlertIconClass = (type) => {
  switch(type) {
    case 'critical': return 'bg-red-100 text-red-600'
    case 'warning': return 'bg-yellow-100 text-yellow-600'
    case 'info': return 'bg-blue-100 text-blue-600'
    default: return 'bg-gray-100 text-gray-600'
  }
}

const generateReport = () => {
  console.log('Generating stock report...')
}

const printReport = () => {
  window.print()
}

const exportReport = () => {
  console.log('Exporting to Excel...')
}
</script>