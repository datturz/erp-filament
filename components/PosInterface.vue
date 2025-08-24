<template>
  <div class="h-screen bg-gray-50 flex flex-col">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-4 py-3">
      <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">{{ $t('pos.title') }}</h1>
        <div class="text-sm text-gray-500">
          {{ new Date().toLocaleDateString() }}
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
            :placeholder="$t('pos.search_products')"
            size="lg"
            @input="searchProducts"
          >
            <template #leading>
              <UIcon name="i-heroicons-magnifying-glass" />
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
                    <span class="ml-2 text-sm text-gray-500">{{ $t('inventory.current_stock') }}: {{ product.current_stock }}</span>
                  </div>
                </div>
                <UButton size="sm" variant="soft">{{ $t('common.create') }}</UButton>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel - Cart -->
      <div class="w-80 bg-white border-l flex flex-col">
        <!-- Cart Header -->
        <div class="px-4 py-3 border-b">
          <h2 class="text-lg font-semibold text-gray-900">{{ $t('pos.cart') }}</h2>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="cart.length === 0" class="text-center text-gray-500 mt-8">
            {{ $t('pos.no_items') }}
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
              <span>{{ $t('pos.subtotal') }}:</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>{{ $t('pos.tax') }} (8%):</span>
              <span>${{ tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between font-semibold text-lg">
              <span>{{ $t('pos.total') }}:</span>
              <span>${{ total.toFixed(2) }}</span>
            </div>
          </div>

          <!-- Payment Method -->
          <USelectMenu v-model="paymentMethod" :options="paymentMethods" />

          <!-- Checkout Button -->
          <UButton
            block
            size="lg"
            :loading="processing"
            @click="processTransaction"
          >
            {{ $t('pos.process_sale') }}
          </UButton>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <UModal v-model="showSuccess">
      <div class="p-6 text-center">
        <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
          <UIcon name="i-heroicons-check" class="w-6 h-6 text-green-600" />
        </div>
        <h3 class="text-lg font-semibold mb-2">{{ $t('pos.sale_completed') }}</h3>
        <p class="text-gray-600 mb-4">{{ $t('pos.transaction_number') }}{{ lastTransaction }}</p>
        <p class="text-2xl font-bold text-green-600 mb-6">${{ lastTotal?.toFixed(2) }}</p>
        <UButton @click="resetTransaction">{{ $t('pos.new_transaction') }}</UButton>
      </div>
    </UModal>
  </div>
</template>

<script setup>
const { searchProducts: apiSearch, processSale } = useAPI()

const searchQuery = ref('')
const products = ref([])
const cart = ref([])
const processing = ref(false)
const showSuccess = ref(false)
const lastTransaction = ref('')
const lastTotal = ref(0)

const paymentMethod = ref('cash')
const { t } = useI18n()

const paymentMethods = computed(() => [
  { label: t('pos.payment_cash'), value: 'cash' },
  { label: t('pos.payment_credit'), value: 'credit_card' },
  { label: t('pos.payment_debit'), value: 'debit_card' },
  { label: t('pos.payment_check'), value: 'check' }
])

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

  try {
    products.value = await apiSearch(searchQuery.value)
  } catch (error) {
    console.error('Search failed:', error)
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
    // Show error
    return
  }
  
  cart.value[index].quantity = quantity
}

const processTransaction = async () => {
  processing.value = true
  
  try {
    const saleData = {
      items: cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity
      })),
      payment_method: paymentMethod.value
    }
    
    const result = await processSale(saleData)
    
    lastTransaction.value = result.transaction_number
    lastTotal.value = result.total_amount
    showSuccess.value = true
    
  } catch (error) {
    console.error('Transaction failed:', error)
    // Show error toast
  } finally {
    processing.value = false
  }
}

const resetTransaction = () => {
  cart.value = []
  searchQuery.value = ''
  products.value = []
  showSuccess.value = false
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