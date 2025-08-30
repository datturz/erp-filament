<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Include sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
      <!-- Sidebar content (same as dashboard) -->
      <div class="h-16 px-4 bg-gray-800 flex items-center">
        <span class="text-white font-bold text-xl">PANTS ERP</span>
      </div>
      <nav class="mt-4 px-4">
        <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 mb-1 text-gray-300 rounded hover:bg-gray-800">Dashboard</NuxtLink>
        <NuxtLink to="/products" class="flex items-center px-3 py-2 mb-1 text-white bg-gray-800 rounded">Products</NuxtLink>
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
          <h1 class="text-xl font-semibold">Products Management</h1>
          <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Product
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
              placeholder="Search products..."
              class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <select v-model="filterCategory" class="px-3 py-2 border rounded">
              <option value="">All Categories</option>
              <option value="Denim">Denim</option>
              <option value="Casual">Casual</option>
              <option value="Utility">Utility</option>
              <option value="Formal">Formal</option>
              <option value="Sports">Sports</option>
            </select>
          </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in filteredProducts" :key="product.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ product.sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.category }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">${{ product.price }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="product.stock < 20 ? 'text-red-600 font-semibold' : ''">
                    {{ product.stock }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.size }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ product.color }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                  <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Delete</button>
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
        <h2 class="text-lg font-semibold mb-4">{{ showEditModal ? 'Edit Product' : 'Add New Product' }}</h2>
        <form @submit.prevent="saveProduct">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
            <input v-model="formData.sku" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
            <input v-model="formData.name" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select v-model="formData.category" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Select Category</option>
              <option value="Denim">Denim</option>
              <option value="Casual">Casual</option>
              <option value="Utility">Utility</option>
              <option value="Formal">Formal</option>
              <option value="Sports">Sports</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
              <input v-model="formData.price" type="number" step="0.01" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input v-model="formData.stock" type="number" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
              <select v-model="formData.size" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Size</option>
                <option value="XS">XS</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="XXL">XXL</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
              <input v-model="formData.color" type="text" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
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
const filterCategory = ref('')
const products = ref([])
const formData = ref({
  id: null,
  sku: '',
  name: '',
  category: '',
  price: 0,
  stock: 0,
  size: '',
  color: ''
})

// Fetch products from API
const fetchProducts = async () => {
  try {
    const response = await fetch('https://jubilant-prosperity-production.up.railway.app/api/v1/products.php')
    const data = await response.json()
    if (data.success) {
      products.value = data.data
    }
  } catch (error) {
    console.error('Error fetching products:', error)
    // Use mock data as fallback
    products.value = [
      {id: 1, sku: 'SKU-001', name: 'Classic Jeans', category: 'Denim', price: 89.99, stock: 245, size: 'M', color: 'Blue'},
      {id: 2, sku: 'SKU-002', name: 'Slim Fit Chinos', category: 'Casual', price: 69.99, stock: 189, size: 'L', color: 'Khaki'},
      {id: 3, sku: 'SKU-003', name: 'Cargo Pants', category: 'Utility', price: 79.99, stock: 12, size: 'S', color: 'Green'},
      {id: 4, sku: 'SKU-004', name: 'Formal Trousers', category: 'Formal', price: 99.99, stock: 67, size: 'M', color: 'Black'},
      {id: 5, sku: 'SKU-005', name: 'Jogger Pants', category: 'Sports', price: 59.99, stock: 134, size: 'XL', color: 'Gray'}
    ]
  }
}

// Filtered products based on search and category
const filteredProducts = computed(() => {
  let result = products.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.sku.toLowerCase().includes(query)
    )
  }
  
  if (filterCategory.value) {
    result = result.filter(p => p.category === filterCategory.value)
  }
  
  return result
})

const editProduct = (product) => {
  formData.value = { ...product }
  showEditModal.value = true
}

const saveProduct = async () => {
  try {
    if (showEditModal.value) {
      // Update product
      const response = await fetch(`https://jubilant-prosperity-production.up.railway.app/api/v1/products.php/${formData.value.id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        const index = products.value.findIndex(p => p.id === formData.value.id)
        products.value[index] = formData.value
      }
    } else {
      // Create new product
      const response = await fetch('https://jubilant-prosperity-production.up.railway.app/api/v1/products.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(formData.value)
      })
      const data = await response.json()
      if (data.success) {
        products.value.push(data.data)
      }
    }
    closeModal()
  } catch (error) {
    console.error('Error saving product:', error)
    // For demo, just add/update locally
    if (showEditModal.value) {
      const index = products.value.findIndex(p => p.id === formData.value.id)
      products.value[index] = formData.value
    } else {
      formData.value.id = products.value.length + 1
      products.value.push({...formData.value})
    }
    closeModal()
  }
}

const deleteProduct = async (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    try {
      const response = await fetch(`https://jubilant-prosperity-production.up.railway.app/api/v1/products.php/${id}`, {
        method: 'DELETE'
      })
      const data = await response.json()
      if (data.success) {
        products.value = products.value.filter(p => p.id !== id)
      }
    } catch (error) {
      console.error('Error deleting product:', error)
      // For demo, just remove locally
      products.value = products.value.filter(p => p.id !== id)
    }
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  formData.value = {
    id: null,
    sku: '',
    name: '',
    category: '',
    price: 0,
    stock: 0,
    size: '',
    color: ''
  }
}

onMounted(() => {
  fetchProducts()
})
</script>