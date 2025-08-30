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
        <NuxtLink to="/sales-history" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Sales History</NuxtLink>
        <NuxtLink to="/stock-management" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Stock Management</NuxtLink>
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
            <h1 class="text-2xl font-bold text-gray-800">Sales History</h1>
          </div>
          <div class="flex gap-2">
            <button @click="exportData" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
              Export
            </button>
            <NuxtLink to="/pos" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              New Sale
            </NuxtLink>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Date Range & Summary -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
              <select v-model="paymentFilter" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Methods</option>
                <option value="cash">Cash</option>
                <option value="credit_card">Credit Card</option>
                <option value="debit_card">Debit Card</option>
                <option value="bank_transfer">Bank Transfer</option>
              </select>
            </div>
            <div class="flex items-end">
              <button @click="applyFilter" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Apply Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Sales</p>
            <p class="text-2xl font-bold">${{ totalSales.toLocaleString() }}</p>
            <p class="text-xs text-green-600">+12.5% from last period</p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Transactions</p>
            <p class="text-2xl font-bold">{{ totalTransactions }}</p>
            <p class="text-xs text-green-600">+8% from last period</p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Average Sale</p>
            <p class="text-2xl font-bold">${{ averageSale }}</p>
            <p class="text-xs text-green-600">+5% from last period</p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Top Product</p>
            <p class="text-lg font-bold">Classic Jeans</p>
            <p class="text-xs text-gray-600">45 units sold</p>
          </div>
        </div>

        <!-- Sales Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-3 border-b">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search by transaction ID, customer name..."
              class="w-full md:w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="sale in filteredSales" :key="sale.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ sale.transactionId }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ sale.date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ sale.customer }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ sale.items }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">${{ sale.total }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span class="capitalize">{{ sale.payment }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ sale.store }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(sale.status)" class="px-2 py-1 text-xs rounded-full">
                    {{ sale.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="viewDetails(sale)" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                  <button @click="printReceipt(sale)" class="text-gray-600 hover:text-gray-900 mr-3">Print</button>
                  <button v-if="sale.status === 'completed'" @click="refundSale(sale)" class="text-red-600 hover:text-red-900">Refund</button>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- Pagination -->
          <div class="px-6 py-3 border-t flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, totalItems) }} of {{ totalItems }} results
            </div>
            <div class="flex gap-2">
              <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">
                Previous
              </button>
              <button @click="currentPage++" :disabled="currentPage * itemsPerPage >= totalItems" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sale Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">Transaction Details</h2>
          <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div v-if="selectedSale">
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <p class="text-sm text-gray-600">Transaction ID</p>
              <p class="font-medium">{{ selectedSale.transactionId }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Date & Time</p>
              <p class="font-medium">{{ selectedSale.date }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Customer</p>
              <p class="font-medium">{{ selectedSale.customer }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Payment Method</p>
              <p class="font-medium capitalize">{{ selectedSale.payment }}</p>
            </div>
          </div>
          
          <h3 class="font-semibold mb-2">Items Purchased</h3>
          <table class="w-full mb-4">
            <thead class="border-b">
              <tr>
                <th class="text-left py-2 text-sm">Product</th>
                <th class="text-right py-2 text-sm">Qty</th>
                <th class="text-right py-2 text-sm">Price</th>
                <th class="text-right py-2 text-sm">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in selectedSale.itemDetails" :key="item.id" class="border-b">
                <td class="py-2 text-sm">{{ item.name }}</td>
                <td class="py-2 text-sm text-right">{{ item.quantity }}</td>
                <td class="py-2 text-sm text-right">${{ item.price }}</td>
                <td class="py-2 text-sm text-right">${{ (item.quantity * item.price).toFixed(2) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="py-2 text-right font-semibold">Subtotal:</td>
                <td class="py-2 text-right font-semibold">${{ selectedSale.subtotal }}</td>
              </tr>
              <tr>
                <td colspan="3" class="py-2 text-right">Tax (8%):</td>
                <td class="py-2 text-right">${{ selectedSale.tax }}</td>
              </tr>
              <tr>
                <td colspan="3" class="py-2 text-right font-bold">Total:</td>
                <td class="py-2 text-right font-bold text-lg">${{ selectedSale.total }}</td>
              </tr>
            </tfoot>
          </table>
          
          <div class="flex justify-end gap-2">
            <button @click="printReceipt(selectedSale)" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
              Print Receipt
            </button>
            <button @click="showDetailsModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const sidebarOpen = ref(false)
const searchQuery = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const selectedStore = ref('')
const paymentFilter = ref('')
const showDetailsModal = ref(false)
const selectedSale = ref(null)

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)
const totalItems = ref(125)

// Summary data
const totalSales = ref(45678)
const totalTransactions = ref(125)
const averageSale = ref(365)

// Sales data
const sales = ref([
  {
    id: 1,
    transactionId: 'TRX-001',
    date: '2024-01-15 10:30 AM',
    customer: 'John Doe',
    items: 3,
    total: 289.99,
    subtotal: 268.51,
    tax: 21.48,
    payment: 'cash',
    store: 'Main Store',
    status: 'completed',
    itemDetails: [
      {id: 1, name: 'Classic Jeans', quantity: 2, price: 89.99},
      {id: 2, name: 'Slim Fit Chinos', quantity: 1, price: 69.99}
    ]
  },
  {
    id: 2,
    transactionId: 'TRX-002',
    date: '2024-01-15 11:45 AM',
    customer: 'Jane Smith',
    items: 2,
    total: 149.98,
    subtotal: 138.87,
    tax: 11.11,
    payment: 'credit_card',
    store: 'Branch 1',
    status: 'completed',
    itemDetails: [
      {id: 1, name: 'Cargo Pants', quantity: 1, price: 79.99},
      {id: 2, name: 'Jogger Pants', quantity: 1, price: 59.99}
    ]
  },
  {
    id: 3,
    transactionId: 'TRX-003',
    date: '2024-01-15 02:20 PM',
    customer: 'Walk-in Customer',
    items: 1,
    total: 89.99,
    subtotal: 83.32,
    tax: 6.67,
    payment: 'cash',
    store: 'Main Store',
    status: 'completed',
    itemDetails: [
      {id: 1, name: 'Formal Trousers', quantity: 1, price: 89.99}
    ]
  },
  {
    id: 4,
    transactionId: 'TRX-004',
    date: '2024-01-15 03:15 PM',
    customer: 'Bob Johnson',
    items: 4,
    total: 359.96,
    subtotal: 333.30,
    tax: 26.66,
    payment: 'debit_card',
    store: 'Branch 2',
    status: 'completed',
    itemDetails: [
      {id: 1, name: 'Classic Jeans', quantity: 4, price: 89.99}
    ]
  },
  {
    id: 5,
    transactionId: 'TRX-005',
    date: '2024-01-15 04:30 PM',
    customer: 'Alice Brown',
    items: 2,
    total: 129.98,
    subtotal: 120.35,
    tax: 9.63,
    payment: 'bank_transfer',
    store: 'Main Store',
    status: 'processing',
    itemDetails: [
      {id: 1, name: 'Khaki Pants', quantity: 2, price: 64.99}
    ]
  }
])

// Computed
const filteredSales = computed(() => {
  let result = sales.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(sale => 
      sale.transactionId.toLowerCase().includes(query) || 
      sale.customer.toLowerCase().includes(query)
    )
  }
  
  if (selectedStore.value) {
    result = result.filter(sale => sale.store.toLowerCase().includes(selectedStore.value))
  }
  
  if (paymentFilter.value) {
    result = result.filter(sale => sale.payment === paymentFilter.value)
  }
  
  return result
})

// Methods
const getStatusClass = (status) => {
  switch(status) {
    case 'completed': return 'bg-green-100 text-green-700'
    case 'processing': return 'bg-blue-100 text-blue-700'
    case 'refunded': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const applyFilter = () => {
  // Apply date filter
  console.log('Applying filter...')
}

const exportData = () => {
  // Export sales data
  console.log('Exporting data...')
}

const viewDetails = (sale) => {
  selectedSale.value = sale
  showDetailsModal.value = true
}

const printReceipt = (sale) => {
  console.log('Printing receipt for:', sale.transactionId)
  window.print()
}

const refundSale = (sale) => {
  if (confirm(`Are you sure you want to refund transaction ${sale.transactionId}?`)) {
    console.log('Processing refund for:', sale.transactionId)
  }
}

onMounted(() => {
  // Set default dates
  const today = new Date()
  const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
  
  dateTo.value = today.toISOString().split('T')[0]
  dateFrom.value = lastWeek.toISOString().split('T')[0]
})
</script>