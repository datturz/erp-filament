<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Include sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
      <!-- Sidebar content (same as products) -->
      <div class="h-16 px-4 bg-gray-800 flex items-center">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
      </div>
      <nav class="mt-4 px-4">
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Dashboard</NuxtLink>
        <NuxtLink to="/pos" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">POS</NuxtLink>
        <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Products</NuxtLink>
        <NuxtLink to="/categories" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Categories</NuxtLink>
        <NuxtLink to="/customers" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Customers</NuxtLink>
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
          <h1 class="text-xl font-semibold">Categories Management</h1>
          <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Category
          </button>
        </div>
      </header>

      <!-- Content -->
      <div class="p-6">
        <!-- Search -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search categories..."
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Count</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="category in filteredCategories" :key="category.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ category.name }}</td>
                <td class="px-6 py-4 text-sm">{{ category.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ category.parent || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ category.productCount }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="category.status === 'active' ? 'text-green-600' : 'text-gray-400'">
                    {{ category.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                  <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Delete</button>
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
        <h2 class="text-lg font-semibold mb-4">{{ showEditModal ? 'Edit Category' : 'Add New Category' }}</h2>
        <form @submit.prevent="saveCategory">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input v-model="formData.name" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="formData.description" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
            <select v-model="formData.parent" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">No Parent</option>
              <option v-for="cat in categories.filter(c => c.id !== formData.id)" :key="cat.id" :value="cat.name">
                {{ cat.name }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="formData.status" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
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
const categories = ref([])
const formData = ref({
  id: null,
  name: '',
  description: '',
  parent: '',
  status: 'active',
  productCount: 0
})

// Fetch categories from API
const fetchCategories = async () => {
  try {
    const response = await fetch('/api/v1/categories.php')
    const data = await response.json()
    if (data.success) {
      categories.value = data.data
    }
  } catch (error) {
    console.error('Error fetching categories:', error)
    // Use mock data as fallback
    categories.value = [
      {id: 1, name: 'Denim', description: 'Denim jeans and jackets', parent: '', productCount: 45, status: 'active'},
      {id: 2, name: 'Casual', description: 'Casual wear pants', parent: '', productCount: 32, status: 'active'},
      {id: 3, name: 'Formal', description: 'Formal trousers and suits', parent: '', productCount: 28, status: 'active'},
      {id: 4, name: 'Sports', description: 'Sports and athletic wear', parent: '', productCount: 15, status: 'active'},
      {id: 5, name: 'Utility', description: 'Cargo and utility pants', parent: '', productCount: 12, status: 'active'}
    ]
  }
}

// Filtered categories based on search
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value
  
  const query = searchQuery.value.toLowerCase()
  return categories.value.filter(c => 
    c.name.toLowerCase().includes(query) || 
    c.description.toLowerCase().includes(query)
  )
})

const editCategory = (category) => {
  formData.value = { ...category }
  showEditModal.value = true
}

const saveCategory = async () => {
  try {
    if (showEditModal.value) {
      // Update category
      const response = await fetch(`/api/v1/categories.php/${formData.value.id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        const index = categories.value.findIndex(c => c.id === formData.value.id)
        categories.value[index] = formData.value
      }
    } else {
      // Create new category
      const response = await fetch('/api/v1/categories.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        categories.value.push(data.data)
      }
    }
    closeModal()
  } catch (error) {
    console.error('Error saving category:', error)
    // For demo, just add/update locally
    if (showEditModal.value) {
      const index = categories.value.findIndex(c => c.id === formData.value.id)
      categories.value[index] = formData.value
    } else {
      formData.value.id = categories.value.length + 1
      categories.value.push({...formData.value})
    }
    closeModal()
  }
}

const deleteCategory = async (id) => {
  if (confirm('Are you sure you want to delete this category?')) {
    try {
      const response = await fetch(`/api/v1/categories.php/${id}`, {
        method: 'DELETE'
      })
      const data = await response.json()
      if (data.success) {
        categories.value = categories.value.filter(c => c.id !== id)
      }
    } catch (error) {
      console.error('Error deleting category:', error)
      // For demo, just remove locally
      categories.value = categories.value.filter(c => c.id !== id)
    }
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  formData.value = {
    id: null,
    name: '',
    description: '',
    parent: '',
    status: 'active',
    productCount: 0
  }
}

onMounted(() => {
  fetchCategories()
})
</script>