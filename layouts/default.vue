<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
      <!-- Logo -->
      <div class="flex items-center justify-between h-16 px-4 bg-gray-800">
        <div class="flex items-center">
          <span class="text-white font-bold text-xl">PANTS ERP</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Store Selector -->
      <div class="px-4 py-3 border-b border-gray-800">
        <select v-model="selectedStore" class="w-full px-3 py-2 bg-gray-800 text-white rounded border border-gray-700 focus:outline-none focus:border-blue-500">
          <option value="all">All Stores</option>
          <option value="store1">Store Jakarta</option>
          <option value="store2">Store Bandung</option>
          <option value="store3">Store Surabaya</option>
          <option value="warehouse">Main Warehouse</option>
        </select>
      </div>

      <!-- Navigation -->
      <nav class="mt-4 px-4 overflow-y-auto" style="max-height: calc(100vh - 8rem)">
        <!-- Dashboard -->
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Dashboard
        </NuxtLink>

        <!-- POS -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Point of Sale</h3>
          <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            POS Terminal
          </NuxtLink>
          <NuxtLink to="/sales-history" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Sales History
          </NuxtLink>
          <NuxtLink to="/customers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Customers
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
          <NuxtLink to="/suppliers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
            </svg>
            Suppliers
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
          <NuxtLink to="/transfers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            Stock Transfers
          </NuxtLink>
          <NuxtLink to="/batches" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Production
          </NuxtLink>
          <NuxtLink to="/cycle-count" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Stock Opname
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
          <NuxtLink to="/purchase-orders" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Purchase Orders
          </NuxtLink>
          <NuxtLink to="/invoices" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Invoices
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
          <NuxtLink to="/reports/financial" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Financial Report
          </NuxtLink>
          <NuxtLink to="/reports/production" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a3 3 0 003 3h0a3 3 0 003-3v-1m3-10V4a1 1 0 00-1-1h-1M8 4H7a1 1 0 00-1 1v3m8 0V4M8 8h8m-4-4v8"></path>
            </svg>
            Production Report
          </NuxtLink>
        </div>

        <!-- Settings -->
        <div class="mb-4">
          <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Settings</h3>
          <NuxtLink to="/users" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Users
          </NuxtLink>
          <NuxtLink to="/stores" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Store Management
          </NuxtLink>
          <NuxtLink to="/settings" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Settings
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
          
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="relative text-gray-600 hover:text-gray-900">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
            </button>
            
            <!-- User Menu -->
            <div class="relative user-menu-container">
              <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-sm rounded-full focus:outline-none">
                <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center">
                  <span class="text-white font-medium">{{ userInitial }}</span>
                </div>
                <span class="ml-2 text-gray-700">{{ userEmail }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <NuxtLink to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</NuxtLink>
                <NuxtLink to="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</NuxtLink>
                <hr class="my-1">
                <button @click="handleLogout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const selectedStore = ref('all')
const userEmail = ref('')

const userInitial = computed(() => {
  return userEmail.value ? userEmail.value[0].toUpperCase() : 'U'
})

// Handle click outside to close menu
const handleClickOutside = (event) => {
  const userMenuEl = document.querySelector('.user-menu-container')
  if (userMenuEl && !userMenuEl.contains(event.target)) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    const authToken = document.cookie.split('; ').find(row => row.startsWith('auth-token='))
    if (authToken) {
      try {
        const token = authToken.split('=')[1]
        const decoded = atob(token)
        const email = decoded.split(':')[0]
        userEmail.value = email
      } catch (e) {
        console.error('Error decoding token:', e)
      }
    }
    
    // Add click outside listener
    document.addEventListener('click', handleClickOutside)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    document.removeEventListener('click', handleClickOutside)
  }
})

const handleLogout = () => {
  document.cookie = 'auth-token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
  window.location.href = '/login'
}
</script>