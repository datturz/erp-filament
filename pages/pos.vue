<template>
  <div class="h-screen bg-gray-50 flex flex-col">
    <!-- Enhanced Header with Offline Status -->
    <div class="bg-white shadow-sm border-b px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <UButton variant="ghost" @click="$router.back()">
            <UIcon name="i-heroicons-arrow-left" />
          </UButton>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">Point of Sale</h1>
            <div class="flex items-center space-x-2 text-sm">
              <span class="text-gray-500">{{ new Date().toLocaleDateString() }}</span>
              <div class="flex items-center">
                <div 
                  class="w-2 h-2 rounded-full mr-1"
                  :class="isOnline ? 'bg-green-500' : 'bg-red-500'"
                ></div>
                <span :class="isOnline ? 'text-green-600' : 'text-red-600'">
                  {{ isOnline ? 'Online' : 'Offline' }}
                </span>
                <span v-if="hasPendingActions" class="text-orange-600 ml-2">
                  ({{ syncStatus.pendingActions }} pending)
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <!-- Barcode Scanner Button -->
          <UButton variant="outline" size="sm" @click="showScanner = true">
            <UIcon name="i-heroicons-camera" class="mr-1" />
            Scan
          </UButton>
          
          <!-- Sync Button -->
          <UButton 
            v-if="hasPendingActions"
            variant="outline" 
            size="sm" 
            :loading="syncStatus.syncInProgress"
            @click="syncActions"
          >
            <UIcon name="i-heroicons-arrow-path" class="mr-1" />
            Sync
          </UButton>
        </div>
      </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
      <!-- Left Panel - Product Search & Cart -->
      <div class="flex-1 flex flex-col">
        <!-- Search -->
        <div class="p-4 bg-white border-b">
          <UInput
            v-model="searchQuery"
            placeholder="Search products (name, SKU, barcode) or scan"
            size="lg"
            @input="searchProducts"
          >
            <template #leading>
              <UIcon name="i-heroicons-magnifying-glass" />
            </template>
            <template #trailing>
              <UButton variant="ghost" size="xs" @click="showScanner = true">
                <UIcon name="i-heroicons-camera" />
              </UButton>
            </template>
          </UInput>
        </div>

        <!-- Product Results -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="products.length" class="grid grid-cols-1 gap-2">
            <div
              v-for="product in products"
              :key="product.id"
              class="bg-white p-3 rounded-lg border cursor-pointer hover:bg-gray-50"
              @click="addToCart(product)"
            >
              <div class="flex justify-between items-center">
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900">{{ product.name }}</h3>
                  <p class="text-sm text-gray-500">{{ product.sku }}</p>
                  <div class="flex items-center mt-1">
                    <span class="text-lg font-semibold text-green-600">${{ product.retail_price }}</span>
                    <span class="ml-2 text-sm text-gray-500">Stock: {{ product.current_stock }}</span>
                  </div>
                </div>
                <UButton size="sm" variant="soft">Add</UButton>
              </div>
            </div>
          </div>
          
          <div v-else-if="searchQuery && !searching" class="text-center text-gray-500 mt-8">
            No products found
          </div>
        </div>
      </div>

      <!-- Right Panel - Cart -->
      <div class="w-80 bg-white border-l flex flex-col">
        <!-- Cart Header -->
        <div class="px-4 py-3 border-b">
          <h2 class="text-lg font-semibold text-gray-900">Cart</h2>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="cart.length === 0" class="text-center text-gray-500 mt-8">
            <UIcon name="i-heroicons-shopping-cart" class="h-16 w-16 text-gray-300 mx-auto mb-4" />
            <p>No items in cart</p>
            <p class="text-sm">Scan or search to add products</p>
          </div>
          
          <div v-else class="space-y-3">
            <div
              v-for="(item, index) in cart"
              :key="`${item.id}-${index}`"
              class="bg-gray-50 p-3 rounded-lg"
            >
              <div class="flex justify-between items-start mb-2">
                <h4 class="font-medium text-gray-900 text-sm">{{ item.name }}</h4>
                <UButton
                  size="xs"
                  variant="ghost"
                  color="red"
                  @click="removeFromCart(index)"
                >
                  <UIcon name="i-heroicons-x-mark" />
                </UButton>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <UButton
                    size="xs"
                    variant="outline"
                    @click="updateQuantity(index, item.quantity - 1)"
                  >
                    -
                  </UButton>
                  <span class="text-sm font-medium">{{ item.quantity }}</span>
                  <UButton
                    size="xs"
                    variant="outline"
                    @click="updateQuantity(index, item.quantity + 1)"
                  >
                    +
                  </UButton>
                </div>
                
                <div class="text-right">
                  <div class="text-sm text-gray-500">${{ item.retail_price }} each</div>
                  <div class="font-semibold">${{ (item.retail_price * item.quantity).toFixed(2) }}</div>
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
          <USelectMenu v-model="paymentMethod" :options="paymentMethods" />

          <!-- Customer Info (Optional) -->
          <div class="space-y-2">
            <UInput v-model="customerName" placeholder="Customer name (optional)" size="sm" />
            <UInput v-model="customerPhone" placeholder="Phone (optional)" size="sm" />
          </div>

          <!-- Checkout Button -->
          <UButton
            block
            size="lg"
            :loading="processing"
            @click="processTransaction"
          >
            {{ isOnline ? 'Process Sale' : 'Process Sale (Offline)' }}
          </UButton>
        </div>
      </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <UModal v-model="showScanner">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Scan Product Barcode</h3>
          <UButton variant="ghost" @click="showScanner = false">
            <UIcon name="i-heroicons-x-mark" />
          </UButton>
        </div>
        
        <BarcodeScanner 
          mode="product"
          @scan-result="handleScanResult"
        />
      </div>
    </UModal>

    <!-- Success Modal -->
    <UModal v-model="showSuccess">
      <div class="p-6 text-center">
        <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
          <UIcon name="i-heroicons-check" class="w-6 h-6 text-green-600" />
        </div>
        <h3 class="text-lg font-semibold mb-2">Sale Completed!</h3>
        <p class="text-gray-600 mb-2">Transaction #{{ lastTransaction }}</p>
        <p v-if="!isOnline" class="text-sm text-orange-600 mb-2">
          Saved offline - will sync when online
        </p>
        <p class="text-2xl font-bold text-green-600 mb-6">${{ lastTotal?.toFixed(2) }}</p>
        <div class="space-y-2">
          <UButton block @click="resetTransaction">New Transaction</UButton>
          <UButton variant="outline" block @click="printReceipt">Print Receipt</UButton>
        </div>
      </div>
    </UModal>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

const { searchProducts: apiSearch, processSale } = useAPI()
const { 
  syncStatus, 
  isOnline, 
  hasPendingActions, 
  queueSale, 
  syncActions,
  executeWithOfflineSupport 
} = useOfflineSync()

const searchQuery = ref('')
const products = ref([])
const cart = ref([])
const processing = ref(false)
const searching = ref(false)
const showSuccess = ref(false)
const showScanner = ref(false)
const lastTransaction = ref('')
const lastTotal = ref(0)

const paymentMethod = ref('cash')
const customerName = ref('')
const customerPhone = ref('')

const paymentMethods = [
  { label: 'Cash', value: 'cash' },
  { label: 'Credit Card', value: 'credit_card' },
  { label: 'Debit Card', value: 'debit_card' },
  { label: 'Check', value: 'check' }
]

// Computed
const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.retail_price * item.quantity), 0)
})

const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)

// Methods
const searchProducts = debounce(async () => {
  if (!searchQuery.value.trim()) {
    products.value = []
    return
  }

  searching.value = true
  try {
    // Use offline-capable search
    products.value = await executeWithOfflineSupport(
      `/products/search?q=${encodeURIComponent(searchQuery.value)}`,
      {},
      [] // Empty fallback when offline
    )
  } catch (error) {
    console.error('Search failed:', error)
    products.value = []
  } finally {
    searching.value = false
  }
}, 300)

const addToCart = (product) => {
  const existingIndex = cart.value.findIndex(item => item.id === product.id)
  
  if (existingIndex >= 0) {
    cart.value[existingIndex].quantity += 1
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
  if (quantity > item.current_stock) {
    // Show error notification
    return
  }
  
  cart.value[index].quantity = quantity
}

const handleScanResult = async (scanResult) => {
  showScanner.value = false
  searchQuery.value = scanResult.code
  
  // Trigger search
  await searchProducts()
  
  // Auto-add if single result
  if (products.value.length === 1) {
    addToCart(products.value[0])
  }
}

const processTransaction = async () => {
  processing.value = true
  
  try {
    const saleData = {
      items: cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity
      })),
      payment_method: paymentMethod.value,
      customer_name: customerName.value || null,
      customer_phone: customerPhone.value || null,
      timestamp: new Date().toISOString()
    }
    
    if (isOnline.value) {
      // Process online
      const result = await processSale(saleData)
      lastTransaction.value = result.transaction_number
      lastTotal.value = result.total_amount
    } else {
      // Queue for offline sync
      const actionId = queueSale(saleData)
      lastTransaction.value = `OFFLINE-${actionId.slice(-6)}`
      lastTotal.value = total.value
    }
    
    showSuccess.value = true
    
  } catch (error) {
    console.error('Transaction failed:', error)
    
    if (!isOnline.value) {
      // Still queue offline even if processing fails
      const actionId = queueSale(saleData)
      lastTransaction.value = `OFFLINE-${actionId.slice(-6)}`
      lastTotal.value = total.value
      showSuccess.value = true
    }
  } finally {
    processing.value = false
  }
}

const resetTransaction = () => {
  cart.value = []
  searchQuery.value = ''
  products.value = []
  customerName.value = ''
  customerPhone.value = ''
  showSuccess.value = false
}

const printReceipt = () => {
  // Implement receipt printing
  window.print()
}

// Utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}
</script>