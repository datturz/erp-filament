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
        <NuxtLink to="/customers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Customers</NuxtLink>
        <NuxtLink to="/suppliers" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Suppliers</NuxtLink>
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
          <h1 class="text-xl font-semibold">Suppliers Management</h1>
          <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Supplier
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
              placeholder="Search suppliers..."
              class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <select v-model="filterStatus" class="px-3 py-2 border rounded">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="pending">Pending</option>
            </select>
          </div>
        </div>

        <!-- Suppliers Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Categories</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="supplier in filteredSuppliers" :key="supplier.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ supplier.company }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ supplier.contactPerson }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ supplier.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ supplier.phone }}</td>
                <td class="px-6 py-4 text-sm">{{ supplier.categories }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="getStatusClass(supplier.status)">
                    {{ supplier.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="editSupplier(supplier)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                  <button @click="deleteSupplier(supplier.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h2 class="text-lg font-semibold mb-4">{{ showEditModal ? 'Edit Supplier' : 'Add New Supplier' }}</h2>
        <form @submit.prevent="saveSupplier">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
              <input v-model="formData.company" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
              <input v-model="formData.contactPerson" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="formData.email" type="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input v-model="formData.phone" type="tel" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea v-model="formData.address" rows="2" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Categories</label>
            <input v-model="formData.categories" type="text" placeholder="e.g., Denim, Casual, Formal" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
              <select v-model="formData.paymentTerms" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Net 30">Net 30</option>
                <option value="Net 60">Net 60</option>
                <option value="COD">COD</option>
                <option value="Prepaid">Prepaid</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="formData.status" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
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
const filterStatus = ref('')
const suppliers = ref([])
const formData = ref({
  id: null,
  company: '',
  contactPerson: '',
  email: '',
  phone: '',
  address: '',
  categories: '',
  paymentTerms: 'Net 30',
  status: 'active'
})

// Fetch suppliers from API
const fetchSuppliers = async () => {
  try {
    const response = await fetch('/api/v1/suppliers.php')
    const data = await response.json()
    if (data.success) {
      suppliers.value = data.data
    }
  } catch (error) {
    console.error('Error fetching suppliers:', error)
    // Use mock data as fallback
    suppliers.value = [
      {id: 1, company: 'Denim World Ltd', contactPerson: 'Robert Brown', email: 'robert@denimworld.com', phone: '+1234567890', categories: 'Denim, Jeans', paymentTerms: 'Net 30', status: 'active', address: '123 Textile Ave'},
      {id: 2, company: 'Fashion Fabrics Inc', contactPerson: 'Sarah Wilson', email: 'sarah@fashionfabrics.com', phone: '+0987654321', categories: 'Casual, Formal', paymentTerms: 'Net 60', status: 'active', address: '456 Fashion St'},
      {id: 3, company: 'Quality Textiles', contactPerson: 'James Miller', email: 'james@qualitytextiles.com', phone: '+1122334455', categories: 'All Categories', paymentTerms: 'COD', status: 'active', address: '789 Industry Blvd'},
      {id: 4, company: 'SportWear Supplies', contactPerson: 'Emma Davis', email: 'emma@sportwear.com', phone: '+5544332211', categories: 'Sports, Athletic', paymentTerms: 'Prepaid', status: 'pending', address: '321 Sports Way'},
      {id: 5, company: 'Premium Materials Co', contactPerson: 'Michael Chen', email: 'michael@premium.com', phone: '+9988776655', categories: 'Premium, Luxury', paymentTerms: 'Net 30', status: 'active', address: '555 Luxury Lane'}
    ]
  }
}

// Filtered suppliers based on search and filter
const filteredSuppliers = computed(() => {
  let result = suppliers.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(s => 
      s.company.toLowerCase().includes(query) || 
      s.contactPerson.toLowerCase().includes(query) ||
      s.email.toLowerCase().includes(query)
    )
  }
  
  if (filterStatus.value) {
    result = result.filter(s => s.status === filterStatus.value)
  }
  
  return result
})

const getStatusClass = (status) => {
  switch(status) {
    case 'active': return 'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800'
    case 'inactive': return 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800'
    case 'pending': return 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800'
    default: return 'px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800'
  }
}

const editSupplier = (supplier) => {
  formData.value = { ...supplier }
  showEditModal.value = true
}

const saveSupplier = async () => {
  try {
    if (showEditModal.value) {
      // Update supplier
      const response = await fetch(`/api/v1/suppliers.php/${formData.value.id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        const index = suppliers.value.findIndex(s => s.id === formData.value.id)
        suppliers.value[index] = formData.value
      }
    } else {
      // Create new supplier
      const response = await fetch('/api/v1/suppliers.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        suppliers.value.push(data.data)
      }
    }
    closeModal()
  } catch (error) {
    console.error('Error saving supplier:', error)
    // For demo, just add/update locally
    if (showEditModal.value) {
      const index = suppliers.value.findIndex(s => s.id === formData.value.id)
      suppliers.value[index] = formData.value
    } else {
      formData.value.id = suppliers.value.length + 1
      suppliers.value.push({...formData.value})
    }
    closeModal()
  }
}

const deleteSupplier = async (id) => {
  if (confirm('Are you sure you want to delete this supplier?')) {
    try {
      const response = await fetch(`/api/v1/suppliers.php/${id}`, {
        method: 'DELETE'
      })
      const data = await response.json()
      if (data.success) {
        suppliers.value = suppliers.value.filter(s => s.id !== id)
      }
    } catch (error) {
      console.error('Error deleting supplier:', error)
      // For demo, just remove locally
      suppliers.value = suppliers.value.filter(s => s.id !== id)
    }
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  formData.value = {
    id: null,
    company: '',
    contactPerson: '',
    email: '',
    phone: '',
    address: '',
    categories: '',
    paymentTerms: 'Net 30',
    status: 'active'
  }
}

onMounted(() => {
  fetchSuppliers()
})
</script>