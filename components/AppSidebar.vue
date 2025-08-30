<template>
  <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <!-- Logo -->
    <div class="h-16 px-6 bg-gray-800 flex items-center justify-between">
      <span class="text-white font-bold text-xl">PANTS ERP</span>
      <button @click="$emit('toggle')" class="lg:hidden text-gray-400">
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
      <NuxtLink to="/dashboard" :class="getLinkClass('/dashboard')">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
      </NuxtLink>

      <!-- POS Section -->
      <div class="mb-4 mt-4">
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Point of Sale</p>
        <NuxtLink to="/pos" :class="getLinkClass('/pos')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          POS Terminal
        </NuxtLink>
        <NuxtLink to="/sales-history" :class="getLinkClass('/sales-history')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Sales History
        </NuxtLink>
      </div>

      <!-- Inventory -->
      <div class="mb-4">
        <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Inventory</h3>
        <NuxtLink to="/stock-management" :class="getLinkClass('/stock-management')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          Stock Management
        </NuxtLink>
        <NuxtLink to="/products" :class="getLinkClass('/products')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
          </svg>
          Products
        </NuxtLink>
        <NuxtLink to="/categories" :class="getLinkClass('/categories')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          Categories
        </NuxtLink>
      </div>

      <!-- Warehouse -->
      <div class="mb-4">
        <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Warehouse</h3>
        <NuxtLink to="/incoming-goods" :class="getLinkClass('/incoming-goods')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
          </svg>
          Incoming Goods
        </NuxtLink>
        <NuxtLink to="/outgoing-goods" :class="getLinkClass('/outgoing-goods')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
          </svg>
          Outgoing Goods
        </NuxtLink>
      </div>

      <!-- Finance -->
      <div class="mb-4">
        <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Finance</h3>
        <NuxtLink to="/income" :class="getLinkClass('/income')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Income
        </NuxtLink>
        <NuxtLink to="/expenses" :class="getLinkClass('/expenses')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          Expenses
        </NuxtLink>
      </div>

      <!-- Reports -->
      <div class="mb-4">
        <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Reports</h3>
        <NuxtLink to="/sales-report" :class="getLinkClass('/sales-report')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          Sales Report
        </NuxtLink>
        <NuxtLink to="/stock-report" :class="getLinkClass('/stock-report')">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
          </svg>
          Stock Report
        </NuxtLink>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  sidebarOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle'])

const route = useRoute()
const selectedStore = ref('all')

const getLinkClass = (path) => {
  const isActive = route.path === path
  return `flex items-center px-3 py-2 mb-1 rounded transition-colors ${
    isActive 
      ? 'text-white bg-gray-800' 
      : 'text-gray-300 hover:bg-gray-800 hover:text-white'
  }`
}
</script>