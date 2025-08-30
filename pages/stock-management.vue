<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Sidebar (same as dashboard) -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
      <div class="h-16 px-6 bg-gray-800 flex items-center">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
      </div>
      <nav class="mt-4 px-4">
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Dashboard</NuxtLink>
        <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">POS</NuxtLink>
        <NuxtLink to="/stock-management" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Stock Management</NuxtLink>
        <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Products</NuxtLink>
        <NuxtLink to="/categories" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Categories</NuxtLink>
        <NuxtLink to="/customers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Customers</NuxtLink>
        <NuxtLink to="/suppliers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Suppliers</NuxtLink>
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
            <h1 class="text-2xl font-bold text-gray-800">Stock Management</h1>
          </div>
          <div class="flex gap-2">
            <button @click="showAdjustModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Adjust Stock
            </button>
            <button @click="showTransferModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              Transfer Stock
            </button>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Products</p>
                <p class="text-2xl font-bold">{{ totalProducts }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Low Stock Items</p>
                <p class="text-2xl font-bold text-red-600">{{ lowStockItems }}</p>
              </div>
              <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Out of Stock</p>
                <p class="text-2xl font-bold text-orange-600">{{ outOfStock }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Value</p>
                <p class="text-2xl font-bold">${{ totalValue.toLocaleString() }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
          <div class="flex gap-4">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search by product name or SKU..."
              class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <select v-model="filterCategory" class="px-4 py-2 border rounded-lg">
              <option value="">All Categories</option>
              <option value="Denim">Denim</option>
              <option value="Casual">Casual</option>
              <option value="Formal">Formal</option>
              <option value="Sports">Sports</option>
              <option value="Utility">Utility</option>
            </select>
            <select v-model="filterStatus" class="px-4 py-2 border rounded-lg">
              <option value="">All Status</option>
              <option value="in-stock">In Stock</option>
              <option value="low-stock">Low Stock</option>
              <option value="out-of-stock">Out of Stock</option>
            </select>
          </div>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in filteredStock" :key="item.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ item.sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.category }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ item.currentStock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.minStock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">${{ item.price }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${{ (item.currentStock * item.price).toFixed(2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(item)" class="px-2 py-1 text-xs rounded-full">
                    {{ getStatusText(item) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="adjustStock(item)" class="text-blue-600 hover:text-blue-900 mr-3">Adjust</button>
                  <button @click="viewHistory(item)" class="text-gray-600 hover:text-gray-900">History</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Adjust Stock Modal -->
    <div v-if="showAdjustModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Adjust Stock</h2>
        <form @submit.prevent="saveAdjustment">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select v-model="adjustmentData.productId" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Select Product</option>
              <option v-for="product in stockItems" :key="product.id" :value="product.id">
                {{ product.name }} (Current: {{ product.currentStock }})
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type</label>
            <select v-model="adjustmentData.type" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="add">Add Stock</option>
              <option value="remove">Remove Stock</option>
              <option value="set">Set Stock Level</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input v-model.number="adjustmentData.quantity" type="number" min="0" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
            <textarea v-model="adjustmentData.reason" rows="2" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="showAdjustModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Adjustment</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Transfer Stock Modal -->
    <div v-if="showTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Transfer Stock</h2>
        <form @submit.prevent="saveTransfer">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select v-model="transferData.productId" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Select Product</option>
              <option v-for="product in stockItems" :key="product.id" :value="product.id">
                {{ product.name }} (Available: {{ product.currentStock }})
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">From Location</label>
            <select v-model="transferData.fromLocation" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="main">Main Store</option>
              <option value="branch1">Branch 1</option>
              <option value="branch2">Branch 2</option>
              <option value="warehouse">Warehouse</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">To Location</label>
            <select v-model="transferData.toLocation" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="main">Main Store</option>
              <option value="branch1">Branch 1</option>
              <option value="branch2">Branch 2</option>
              <option value="warehouse">Warehouse</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input v-model.number="transferData.quantity" type="number" min="1" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="showTransferModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Transfer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const sidebarOpen = ref(false)
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const showAdjustModal = ref(false)
const showTransferModal = ref(false)

// Stats
const totalProducts = ref(156)
const lowStockItems = ref(12)
const outOfStock = ref(3)
const totalValue = ref(45680)

// Stock items
const stockItems = ref([
  {id: 1, sku: 'SKU-001', name: 'Classic Jeans', category: 'Denim', currentStock: 245, minStock: 50, price: 89.99},
  {id: 2, sku: 'SKU-002', name: 'Slim Fit Chinos', category: 'Casual', currentStock: 189, minStock: 30, price: 69.99},
  {id: 3, sku: 'SKU-003', name: 'Cargo Pants', category: 'Utility', currentStock: 12, minStock: 20, price: 79.99},
  {id: 4, sku: 'SKU-004', name: 'Formal Trousers', category: 'Formal', currentStock: 67, minStock: 25, price: 99.99},
  {id: 5, sku: 'SKU-005', name: 'Jogger Pants', category: 'Sports', currentStock: 0, minStock: 15, price: 59.99},
  {id: 6, sku: 'SKU-006', name: 'Skinny Jeans', category: 'Denim', currentStock: 98, minStock: 40, price: 79.99},
  {id: 7, sku: 'SKU-007', name: 'Khaki Pants', category: 'Casual', currentStock: 156, minStock: 35, price: 64.99},
  {id: 8, sku: 'SKU-008', name: 'Track Pants', category: 'Sports', currentStock: 8, minStock: 20, price: 49.99}
])

// Adjustment data
const adjustmentData = ref({
  productId: '',
  type: 'add',
  quantity: 0,
  reason: ''
})

// Transfer data
const transferData = ref({
  productId: '',
  fromLocation: 'main',
  toLocation: 'branch1',
  quantity: 0
})

// Computed
const filteredStock = computed(() => {
  let result = stockItems.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(item => 
      item.name.toLowerCase().includes(query) || 
      item.sku.toLowerCase().includes(query)
    )
  }
  
  if (filterCategory.value) {
    result = result.filter(item => item.category === filterCategory.value)
  }
  
  if (filterStatus.value) {
    result = result.filter(item => {
      const status = getStockStatus(item)
      return status === filterStatus.value
    })
  }
  
  return result
})

// Methods
const getStockStatus = (item) => {
  if (item.currentStock === 0) return 'out-of-stock'
  if (item.currentStock < item.minStock) return 'low-stock'
  return 'in-stock'
}

const getStatusText = (item) => {
  const status = getStockStatus(item)
  switch(status) {
    case 'out-of-stock': return 'Out of Stock'
    case 'low-stock': return 'Low Stock'
    default: return 'In Stock'
  }
}

const getStatusClass = (item) => {
  const status = getStockStatus(item)
  switch(status) {
    case 'out-of-stock': return 'bg-red-100 text-red-700'
    case 'low-stock': return 'bg-yellow-100 text-yellow-700'
    default: return 'bg-green-100 text-green-700'
  }
}

const adjustStock = (item) => {
  adjustmentData.value.productId = item.id
  showAdjustModal.value = true
}

const viewHistory = (item) => {
  // View stock history
  console.log('View history for:', item)
}

const saveAdjustment = () => {
  // Save stock adjustment
  console.log('Save adjustment:', adjustmentData.value)
  showAdjustModal.value = false
  adjustmentData.value = {
    productId: '',
    type: 'add',
    quantity: 0,
    reason: ''
  }
}

const saveTransfer = () => {
  // Save stock transfer
  console.log('Save transfer:', transferData.value)
  showTransferModal.value = false
  transferData.value = {
    productId: '',
    fromLocation: 'main',
    toLocation: 'branch1',
    quantity: 0
  }
}

onMounted(() => {
  // Fetch stock data
})
</script>