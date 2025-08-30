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
        <NuxtLink to="/sales-report" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Sales Report</NuxtLink>
        <NuxtLink to="/stock-report" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Stock Report</NuxtLink>
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
            <h1 class="text-2xl font-bold text-gray-800">Sales Report</h1>
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
                <option value="daily">Daily Report</option>
                <option value="weekly">Weekly Report</option>
                <option value="monthly">Monthly Report</option>
                <option value="yearly">Yearly Report</option>
                <option value="custom">Custom Range</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
              <input type="date" v-model="dateFrom" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
              <input type="date" v-model="dateTo" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
              <select v-model="selectedStore" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Stores</option>
                <option value="main">Main Store</option>
                <option value="branch1">Branch 1</option>
                <option value="branch2">Branch 2</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button @click="generateReport" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Generate Report
            </button>
          </div>
        </div>

        <!-- Report Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-gray-600">Total Revenue</p>
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ totalRevenue.toLocaleString() }}</p>
            <p class="text-sm text-green-600 mt-2">+15.3% from last period</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-gray-600">Total Orders</p>
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ totalOrders }}</p>
            <p class="text-sm text-blue-600 mt-2">+8.7% from last period</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-gray-600">Average Order Value</p>
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ averageOrderValue }}</p>
            <p class="text-sm text-purple-600 mt-2">+5.2% from last period</p>
          </div>
          
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-gray-600">Items Sold</p>
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ totalItemsSold }}</p>
            <p class="text-sm text-orange-600 mt-2">+12.1% from last period</p>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Sales Trend Chart -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Sales Trend</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
              <!-- Chart would go here -->
              <p class="text-gray-500">Sales Trend Chart</p>
            </div>
          </div>
          
          <!-- Sales by Category -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Sales by Category</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
              <!-- Chart would go here -->
              <p class="text-gray-500">Category Distribution Chart</p>
            </div>
          </div>
        </div>

        <!-- Top Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Top Selling Products</h3>
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% of Total</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(product, index) in topProducts" :key="product.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.category }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.unitsSold }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${{ product.revenue.toLocaleString() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${product.percentage}%`"></div>
                    </div>
                    <span>{{ product.percentage }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Sales by Store -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Sales by Store</h3>
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transactions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items Sold</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Transaction</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Growth</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="store in storePerformance" :key="store.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ store.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ store.transactions }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ store.itemsSold }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${{ store.revenue.toLocaleString() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">${{ store.avgTransaction }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="store.growth > 0 ? 'text-green-600' : 'text-red-600'">
                    {{ store.growth > 0 ? '+' : '' }}{{ store.growth }}%
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
import { ref, onMounted } from 'vue'

const sidebarOpen = ref(false)
const reportType = ref('monthly')
const dateFrom = ref('')
const dateTo = ref('')
const selectedStore = ref('')

// Summary data
const totalRevenue = ref(125680)
const totalOrders = ref(342)
const averageOrderValue = ref(367)
const totalItemsSold = ref(1256)

// Top products data
const topProducts = ref([
  {id: 1, name: 'Classic Jeans', sku: 'SKU-001', category: 'Denim', unitsSold: 145, revenue: 13049, percentage: 35},
  {id: 2, name: 'Slim Fit Chinos', sku: 'SKU-002', category: 'Casual', unitsSold: 98, revenue: 6859, percentage: 25},
  {id: 3, name: 'Formal Trousers', sku: 'SKU-004', category: 'Formal', unitsSold: 67, revenue: 6699, percentage: 20},
  {id: 4, name: 'Cargo Pants', sku: 'SKU-003', category: 'Utility', unitsSold: 45, revenue: 3599, percentage: 12},
  {id: 5, name: 'Jogger Pants', sku: 'SKU-005', category: 'Sports', unitsSold: 38, revenue: 2279, percentage: 8}
])

// Store performance data
const storePerformance = ref([
  {id: 1, name: 'Main Store', transactions: 156, itemsSold: 589, revenue: 68450, avgTransaction: 438, growth: 12.5},
  {id: 2, name: 'Branch 1', transactions: 98, itemsSold: 356, revenue: 35670, avgTransaction: 364, growth: 8.3},
  {id: 3, name: 'Branch 2', transactions: 88, itemsSold: 311, revenue: 21560, avgTransaction: 245, growth: -2.1}
])

// Methods
const generateReport = () => {
  console.log('Generating report...')
  // Generate report based on filters
}

const printReport = () => {
  window.print()
}

const exportReport = () => {
  console.log('Exporting to Excel...')
  // Export report to Excel
}

onMounted(() => {
  // Set default dates
  const today = new Date()
  const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
  
  dateTo.value = today.toISOString().split('T')[0]
  dateFrom.value = firstDayOfMonth.toISOString().split('T')[0]
})
</script>