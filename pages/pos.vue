<template>
  <div class="h-screen bg-gray-50 flex flex-col">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">Point of Sale</h1>
            <div class="flex items-center space-x-2 text-sm">
              <span class="text-gray-500">{{ currentDate }}</span>
              <select v-model="selectedStore" class="text-sm border rounded px-2 py-1">
                <option value="Main Store">Main Store</option>
                <option value="Branch 1">Branch 1</option>
                <option value="Branch 2">Branch 2</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <button @click="clearCart" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
            Clear Cart
          </button>
        </div>
      </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
      <!-- Left Panel - Product Grid -->
      <div class="flex-1 flex flex-col">
        <!-- Search & Categories -->
        <div class="p-4 bg-white border-b">
          <div class="flex gap-2 mb-3">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products by name or SKU..."
              class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
              @input="filterProducts"
            >
            <select v-model="selectedCategory" @change="filterProducts" class="px-3 py-2 border rounded">
              <option value="">All Categories</option>
              <option value="Denim">Denim</option>
              <option value="Casual">Casual</option>
              <option value="Formal">Formal</option>
              <option value="Sports">Sports</option>
              <option value="Utility">Utility</option>
            </select>
          </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="filteredProducts.length" class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div
              v-for="product in filteredProducts"
              :key="product.id"
              class="bg-white p-3 rounded-lg border cursor-pointer hover:shadow-lg transition-shadow"
              @click="addToCart(product)"
            >
              <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded mb-2">
                <div class="flex items-center justify-center h-24">
                  <span class="text-gray-400 text-xs">{{ product.sku }}</span>
                </div>
              </div>
              <h3 class="font-medium text-sm text-gray-900 truncate">{{ product.name }}</h3>
              <p class="text-xs text-gray-500">{{ product.category }}</p>
              <div class="flex justify-between items-center mt-2">
                <span class="text-lg font-bold text-green-600">${{ product.price }}</span>
                <span class="text-xs text-gray-500">Stock: {{ product.stock }}</span>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center text-gray-500 mt-8">
            <p>No products found</p>
            <p class="text-sm mt-2">Try adjusting your search or category filter</p>
          </div>
        </div>
      </div>

      <!-- Right Panel - Cart -->
      <div class="w-96 bg-white border-l flex flex-col">
        <!-- Cart Header -->
        <div class="px-4 py-3 border-b flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-900">Shopping Cart</h2>
          <span class="text-sm text-gray-500">{{ cart.length }} items</span>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="cart.length === 0" class="text-center text-gray-500 mt-8">
            <svg class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p>No items in cart</p>
            <p class="text-sm">Click on products to add them</p>
          </div>
          
          <div v-else class="space-y-3">
            <div
              v-for="(item, index) in cart"
              :key="`${item.id}-${index}`"
              class="bg-gray-50 p-3 rounded-lg"
            >
              <div class="flex justify-between items-start mb-2">
                <h4 class="font-medium text-gray-900 text-sm">{{ item.name }}</h4>
                <button
                  @click="removeFromCart(index)"
                  class="text-red-500 hover:text-red-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <button
                    @click="updateQuantity(index, item.quantity - 1)"
                    class="w-6 h-6 border rounded hover:bg-gray-100"
                  >
                    -
                  </button>
                  <span class="text-sm font-medium w-8 text-center">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(index, item.quantity + 1)"
                    class="w-6 h-6 border rounded hover:bg-gray-100"
                  >
                    +
                  </button>
                </div>
                
                <div class="text-right">
                  <div class="text-xs text-gray-500">${{ item.price }} each</div>
                  <div class="font-semibold">${{ (item.price * item.quantity).toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Summary & Checkout -->
        <div v-if="cart.length > 0" class="border-t p-4 space-y-4">
          <div class="space-y-2">
            <div class="flex justify-between">
              <span>Subtotal:</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Tax (8%):</span>
              <span>${{ tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg">
              <span>Total:</span>
              <span>${{ total.toFixed(2) }}</span>
            </div>
          </div>

          <!-- Payment Method -->
          <select v-model="paymentMethod" class="w-full px-3 py-2 border rounded">
            <option value="cash">Cash</option>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="bank_transfer">Bank Transfer</option>
          </select>

          <!-- Customer Info (Optional) -->
          <div class="space-y-2">
            <select v-model="selectedCustomer" class="w-full px-3 py-2 border rounded text-sm">
              <option value="">Walk-in Customer</option>
              <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
              </option>
            </select>
          </div>

          <!-- Checkout Button -->
          <button
            @click="processTransaction"
            :disabled="processing"
            class="w-full py-3 bg-green-600 text-white rounded font-semibold hover:bg-green-700 disabled:bg-gray-400"
          >
            {{ processing ? 'Processing...' : 'Complete Sale' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccess" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md text-center">
        <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold mb-2">Sale Completed!</h3>
        <p class="text-gray-600 mb-2">Transaction #{{ lastTransaction }}</p>
        <p class="text-2xl font-bold text-green-600 mb-6">${{ lastTotal.toFixed(2) }}</p>
        <div class="space-y-2">
          <button @click="resetTransaction" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            New Transaction
          </button>
          <button @click="printReceipt" class="w-full py-2 border rounded hover:bg-gray-50">
            Print Receipt
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedStore = ref('Main Store')
const selectedCustomer = ref('')
const products = ref([])
const filteredProducts = ref([])
const customers = ref([])
const cart = ref([])
const processing = ref(false)
const showSuccess = ref(false)
const lastTransaction = ref('')
const lastTotal = ref(0)
const paymentMethod = ref('cash')

// Get current date
const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', { 
    weekday: 'short', 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
})

// Computed
const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)

// Methods
const fetchProducts = async () => {
  try {
    const response = await fetch('/api/v1/products.php')
    const data = await response.json()
    if (data.success) {
      products.value = data.data
      filteredProducts.value = data.data
    }
  } catch (error) {
    console.error('Error fetching products:', error)
    // Use mock data as fallback
    products.value = [
      {id: 1, sku: 'SKU-001', name: 'Classic Jeans', category: 'Denim', price: 89.99, stock: 245},
      {id: 2, sku: 'SKU-002', name: 'Slim Fit Chinos', category: 'Casual', price: 69.99, stock: 189},
      {id: 3, sku: 'SKU-003', name: 'Cargo Pants', category: 'Utility', price: 79.99, stock: 12},
      {id: 4, sku: 'SKU-004', name: 'Formal Trousers', category: 'Formal', price: 99.99, stock: 67},
      {id: 5, sku: 'SKU-005', name: 'Jogger Pants', category: 'Sports', price: 59.99, stock: 134},
      {id: 6, sku: 'SKU-006', name: 'Skinny Jeans', category: 'Denim', price: 79.99, stock: 98},
      {id: 7, sku: 'SKU-007', name: 'Khaki Pants', category: 'Casual', price: 64.99, stock: 156},
      {id: 8, sku: 'SKU-008', name: 'Track Pants', category: 'Sports', price: 49.99, stock: 203}
    ]
    filteredProducts.value = products.value
  }
}

const fetchCustomers = async () => {
  try {
    const response = await fetch('/api/v1/customers.php')
    const data = await response.json()
    if (data.success) {
      customers.value = data.data
    }
  } catch (error) {
    console.error('Error fetching customers:', error)
    customers.value = [
      {id: 1, name: 'John Doe'},
      {id: 2, name: 'Jane Smith'},
      {id: 3, name: 'ABC Retail'},
      {id: 4, name: 'Mike Johnson'}
    ]
  }
}

const filterProducts = () => {
  let result = products.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.sku.toLowerCase().includes(query)
    )
  }
  
  if (selectedCategory.value) {
    result = result.filter(p => p.category === selectedCategory.value)
  }
  
  filteredProducts.value = result
}

const addToCart = (product) => {
  const existingIndex = cart.value.findIndex(item => item.id === product.id)
  
  if (existingIndex >= 0) {
    if (cart.value[existingIndex].quantity < product.stock) {
      cart.value[existingIndex].quantity += 1
    }
  } else {
    cart.value.push({
      ...product,
      quantity: 1
    })
  }
}

const removeFromCart = (index) => {
  cart.value.splice(index, 1)
}

const updateQuantity = (index, quantity) => {
  if (quantity <= 0) {
    removeFromCart(index)
    return
  }
  
  const item = cart.value[index]
  if (quantity > item.stock) {
    return
  }
  
  cart.value[index].quantity = quantity
}

const clearCart = () => {
  cart.value = []
}

const processTransaction = async () => {
  if (cart.value.length === 0) return
  
  processing.value = true
  
  try {
    const saleData = {
      store: selectedStore.value,
      items: cart.value.map(item => ({
        product_id: item.id,
        product_name: item.name,
        quantity: item.quantity,
        price: item.price,
        subtotal: item.price * item.quantity
      })),
      payment_method: paymentMethod.value,
      customer_id: selectedCustomer.value || null,
      subtotal: subtotal.value,
      tax: tax.value,
      total: total.value,
      timestamp: new Date().toISOString()
    }
    
    // Try to send to API
    try {
      const response = await fetch('/api/v1/sales.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(saleData)
      })
      const data = await response.json()
      if (data.success) {
        lastTransaction.value = data.transaction_id || Date.now().toString()
      }
    } catch (error) {
      console.error('API error:', error)
      // Generate local transaction ID
      lastTransaction.value = Date.now().toString()
    }
    
    lastTotal.value = total.value
    showSuccess.value = true
    
    // Update product stock locally
    cart.value.forEach(item => {
      const product = products.value.find(p => p.id === item.id)
      if (product) {
        product.stock -= item.quantity
      }
    })
    
  } catch (error) {
    console.error('Transaction failed:', error)
    alert('Transaction failed. Please try again.')
  } finally {
    processing.value = false
  }
}

const resetTransaction = () => {
  cart.value = []
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedCustomer.value = ''
  showSuccess.value = false
  filterProducts()
}

const printReceipt = () => {
  window.print()
}

onMounted(() => {
  fetchProducts()
  fetchCustomers()
})
</script>