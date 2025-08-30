<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Include sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
      <!-- Sidebar content -->
      <div class="h-16 px-4 bg-gray-800 flex items-center">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
      </div>
      <nav class="mt-4 px-4">
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Dashboard</NuxtLink>
        <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">POS</NuxtLink>
        <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Products</NuxtLink>
        <NuxtLink to="/categories" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Categories</NuxtLink>
        <NuxtLink to="/customers" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Customers</NuxtLink>
        <NuxtLink to="/suppliers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Suppliers</NuxtLink>
        <NuxtLink to="/inventory" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Inventory</NuxtLink>
        <NuxtLink to="/income" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Income</NuxtLink>
        <NuxtLink to="/expenses" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Expenses</NuxtLink>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Header -->
      <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
          <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
          <h1 class="text-xl font-semibold">Customers Management</h1>
          <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Customer
          </button>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
          <div class="flex gap-4">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search customers..."
              class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <select v-model="filterType" class="px-3 py-2 border rounded">
              <option value="">All Types</option>
              <option value="regular">Regular</option>
              <option value="vip">VIP</option>
              <option value="wholesale">Wholesale</option>
            </select>
          </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Purchases</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="customer in filteredCustomers" :key="customer.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ customer.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ customer.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ customer.phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="getTypeClass(customer.type)">
                    {{ customer.type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">${{ customer.totalPurchases }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ customer.store }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="editCustomer(customer)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                  <button @click="deleteCustomer(customer.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">{{ showEditModal ? 'Edit Customer' : 'Add New Customer' }}</h2>
        <form @submit.prevent="saveCustomer">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input v-model="formData.name" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="formData.email" type="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input v-model="formData.phone" type="tel" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea v-model="formData.address" rows="2" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
              <select v-model="formData.type" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="regular">Regular</option>
                <option value="vip">VIP</option>
                <option value="wholesale">Wholesale</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
              <select v-model="formData.store" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Main Store">Main Store</option>
                <option value="Branch 1">Branch 1</option>
                <option value="Branch 2">Branch 2</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const sidebarOpen = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const searchQuery = ref('')
const filterType = ref('')
const customers = ref([])
const formData = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  address: '',
  type: 'regular',
  store: 'Main Store',
  totalPurchases: 0
})

// Fetch customers from API
const fetchCustomers = async () => {
  try {
    const response = await fetch('/api/v1/customers.php')
    const data = await response.json()
    if (data.success) {
      customers.value = data.data
    }
  } catch (error) {
    console.error('Error fetching customers:', error)
    // Use mock data as fallback
    customers.value = [
      {id: 1, name: 'John Doe', email: 'john@example.com', phone: '+1234567890', type: 'vip', totalPurchases: 5280, store: 'Main Store', address: '123 Main St'},
      {id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '+0987654321', type: 'regular', totalPurchases: 1250, store: 'Branch 1', address: '456 Oak Ave'},
      {id: 3, name: 'ABC Retail', email: 'contact@abcretail.com', phone: '+1122334455', type: 'wholesale', totalPurchases: 15680, store: 'Main Store', address: '789 Business Blvd'},
      {id: 4, name: 'Mike Johnson', email: 'mike@example.com', phone: '+5544332211', type: 'regular', totalPurchases: 890, store: 'Branch 2', address: '321 Pine St'},
      {id: 5, name: 'XYZ Corp', email: 'orders@xyzcorp.com', phone: '+9988776655', type: 'wholesale', totalPurchases: 28450, store: 'Main Store', address: '555 Corporate Way'}
    ]
  }
}

// Filtered customers based on search and filter
const filteredCustomers = computed(() => {
  let result = customers.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(c => 
      c.name.toLowerCase().includes(query) || 
      c.email.toLowerCase().includes(query) ||
      c.phone.includes(query)
    )
  }
  
  if (filterType.value) {
    result = result.filter(c => c.type === filterType.value)
  }
  
  return result
})

const getTypeClass = (type) => {
  switch(type) {
    case 'vip': return 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800'
    case 'wholesale': return 'px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800'
    default: return 'px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800'
  }
}

const editCustomer = (customer) => {
  formData.value = { ...customer }
  showEditModal.value = true
}

const saveCustomer = async () => {
  try {
    if (showEditModal.value) {
      // Update customer
      const response = await fetch(`/api/v1/customers.php/${formData.value.id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        const index = customers.value.findIndex(c => c.id === formData.value.id)
        customers.value[index] = formData.value
      }
    } else {
      // Create new customer
      const response = await fetch('/api/v1/customers.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        customers.value.push(data.data)
      }
    }
    closeModal()
  } catch (error) {
    console.error('Error saving customer:', error)
    // For demo, just add/update locally
    if (showEditModal.value) {
      const index = customers.value.findIndex(c => c.id === formData.value.id)
      customers.value[index] = formData.value
    } else {
      formData.value.id = customers.value.length + 1
      customers.value.push({...formData.value})
    }
    closeModal()
  }
}

const deleteCustomer = async (id) => {
  if (confirm('Are you sure you want to delete this customer?')) {
    try {
      const response = await fetch(`/api/v1/customers.php/${id}`, {
        method: 'DELETE'
      })
      const data = await response.json()
      if (data.success) {
        customers.value = customers.value.filter(c => c.id !== id)
      }
    } catch (error) {
      console.error('Error deleting customer:', error)
      // For demo, just remove locally
      customers.value = customers.value.filter(c => c.id !== id)
    }
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  formData.value = {
    id: null,
    name: '',
    email: '',
    phone: '',
    address: '',
    type: 'regular',
    store: 'Main Store',
    totalPurchases: 0
  }
}

onMounted(() => {
  fetchCustomers()
})
</script>