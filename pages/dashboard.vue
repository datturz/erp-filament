<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
      <!-- Logo -->
      <div class="h-16 px-6 bg-gray-800 flex items-center justify-between">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Store Selector -->
      <div class="px-4 py-3 border-b border-gray-800">
        <select v-model="selectedStore" class="w-full bg-gray-800 text-white px-3 py-2 rounded border border-gray-700 focus:border-blue-500 focus:outline-none">
          <option value="all">All Stores</option>
          <option value="main">Main Store</option>
          <option value="branch1">Branch 1</option>
          <option value="branch2">Branch 2</option>
        </select>
      </div>

      <!-- Navigation Menu -->
      <nav class="mt-4 px-4 overflow-y-auto" style="max-height: calc(100vh - 12rem)">
        <!-- Dashboard -->
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Dashboard
        </NuxtLink>

        <!-- POS Section -->
        <div class="mb-4 mt-4">
          <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Point of Sale</p>
          <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            POS Terminal
          </NuxtLink>
          <NuxtLink to="/sales-history" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Sales History
          </NuxtLink>
        </div>

        <!-- Inventory -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Inventory</h3>
          <NuxtLink to="/stock-management" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Stock Management
          </NuxtLink>
          <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            Products
          </NuxtLink>
          <NuxtLink to="/categories" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            Categories
          </NuxtLink>
        </div>

        <!-- Warehouse -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Warehouse</h3>
          <NuxtLink to="/incoming-goods" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
            Incoming Goods
          </NuxtLink>
          <NuxtLink to="/outgoing-goods" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
            Outgoing Goods
          </NuxtLink>
        </div>

        <!-- Finance -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Finance</h3>
          <NuxtLink to="/income" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Income
          </NuxtLink>
          <NuxtLink to="/expenses" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Expenses
          </NuxtLink>
        </div>

        <!-- Reports -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Reports</h3>
          <NuxtLink to="/sales-report" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Sales Report
          </NuxtLink>
          <NuxtLink to="/stock-report" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
            Stock Report
          </NuxtLink>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Top Bar -->
      <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
          <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
          <h1 class="text-xl font-semibold text-gray-900">Dashboard Overview</h1>
          <button @click="handleLogout" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 border rounded">
            Logout
          </button>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="p-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-gray-500">Total Sales</p>
                <p class="text-lg font-semibold" v-if="!isLoading">${{ totalSales.toLocaleString() }}</p>
                <p class="text-lg font-semibold" v-else>Loading...</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-gray-500">Inventory</p>
                <p class="text-lg font-semibold" v-if="!isLoading">{{ inventoryCount.toLocaleString() }}</p>
                <p class="text-lg font-semibold" v-else>Loading...</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
              <div class="p-2 bg-yellow-100 rounded">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-gray-500">Production</p>
                <p class="text-lg font-semibold" v-if="!isLoading">{{ productionCount }}</p>
                <p class="text-lg font-semibold" v-else>Loading...</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-gray-500">Staff</p>
                <p class="text-lg font-semibold" v-if="!isLoading">{{ staffCount }}</p>
                <p class="text-lg font-semibold" v-else>Loading...</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Point of Sale</h2>
            <div class="space-y-3">
              <NuxtLink to="/pos" class="block px-4 py-3 bg-blue-600 text-white text-center rounded hover:bg-blue-700">
                Start New Sale
              </NuxtLink>
              <NuxtLink to="/sales-report" class="block px-4 py-3 border border-gray-300 text-center rounded hover:bg-gray-50">
                View Sales Report
              </NuxtLink>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Warehouse Operations</h2>
            <div class="grid grid-cols-2 gap-3">
              <NuxtLink to="/stock-management" class="px-4 py-3 border border-gray-300 text-center rounded hover:bg-gray-50">
                Inventory
              </NuxtLink>
              <NuxtLink to="/batches" class="px-4 py-3 border border-gray-300 text-center rounded hover:bg-gray-50">
                Production
              </NuxtLink>
              <NuxtLink to="/transfers" class="px-4 py-3 border border-gray-300 text-center rounded hover:bg-gray-50">
                Transfers
              </NuxtLink>
              <NuxtLink to="/cycle-count" class="px-4 py-3 border border-gray-300 text-center rounded hover:bg-gray-50">
                Cycle Count
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const sidebarOpen = ref(false)
const selectedStore = ref('all')

// Dashboard metrics - akan diupdate dari API
const totalSales = ref(0)
const inventoryCount = ref(0)
const productionCount = ref(0)
const staffCount = ref(0)

// Loading state
const isLoading = ref(true)

// Fetch dashboard data dari Railway backend
const fetchDashboardData = async () => {
  try {
    isLoading.value = true
    
    // Fetch dari Railway backend
    const response = await fetch('https://jubilant-prosperity-production.up.railway.app/api.php/v1/dashboard', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      
      // Update metrics dari backend
      totalSales.value = data.totalSales || 0
      inventoryCount.value = data.inventoryCount || 0
      productionCount.value = data.productionCount || 0
      staffCount.value = data.staffCount || 0
    } else {
      // Jika API gagal, gunakan data default
      console.log('Using default data - API not available')
      totalSales.value = 12450
      inventoryCount.value = 2345
      productionCount.value = 145
      staffCount.value = 24
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    // Fallback ke data default
    totalSales.value = 12450
    inventoryCount.value = 2345
    productionCount.value = 145
    staffCount.value = 24
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
  
  // Refresh data setiap 30 detik untuk realtime
  setInterval(fetchDashboardData, 30000)
})

const handleLogout = () => {
  document.cookie = 'auth-token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
  window.location.href = '/login'
}
</script>