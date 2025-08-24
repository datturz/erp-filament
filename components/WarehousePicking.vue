<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-4">
          <UButton variant="ghost" @click="$router.back()">
            <UIcon name="i-heroicons-arrow-left" />
          </UButton>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">{{ $t('warehouse.title') }}</h1>
            <p class="text-sm text-gray-500">{{ currentModeText }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <!-- Mode Toggle -->
          <UButton 
            variant="outline" 
            size="sm"
            @click="toggleMode"
          >
            {{ currentMode === 'Transfer' ? $t('warehouse.cycle_count') : $t('warehouse.transfer') }}
          </UButton>
          
          <!-- Scanner -->
          <UButton variant="outline" size="sm" @click="showScanner = true">
            <UIcon name="i-heroicons-camera" />
          </UButton>
        </div>
      </div>
    </header>

    <!-- Mode Selection -->
    <div class="p-4 bg-white border-b">
      <div class="grid grid-cols-2 gap-4">
        <UButton 
          :variant="mode === 'transfer' ? 'solid' : 'outline'"
          block
          @click="setMode('transfer')"
        >
          <UIcon name="i-heroicons-arrow-path" class="mr-2" />
          {{ $t('warehouse.transfer_picking') }}
        </UButton>
        <UButton 
          :variant="mode === 'cycle_count' ? 'solid' : 'outline'"
          block
          @click="setMode('cycle_count')"
        >
          <UIcon name="i-heroicons-clipboard-document-list" class="mr-2" />
          {{ $t('warehouse.cycle_counting') }}
        </UButton>
      </div>
    </div>

    <!-- Transfer Picking Mode -->
    <div v-if="mode === 'transfer'" class="p-4">
      <!-- Pending Transfers -->
      <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('warehouse.pending_shipments') }}</h2>
        
        <div v-if="pendingTransfers.length === 0" class="text-center py-8 text-gray-500">
          {{ $t('warehouse.no_pending_transfers') }}
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="transfer in pendingTransfers"
            :key="transfer.id"
            class="bg-white rounded-lg border p-4 cursor-pointer hover:bg-gray-50"
            @click="startTransferPicking(transfer)"
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium text-gray-900">{{ transfer.transfer_number }}</h3>
                <p class="text-sm text-gray-600">{{ transfer.product.name }}</p>
                <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                  <span>{{ $t('transfers.from_store') }}: {{ transfer.from_store.name }}</span>
                  <span>{{ $t('transfers.to_store') }}: {{ transfer.to_store.name }}</span>
                  <span>{{ $t('transfers.quantity') }}: {{ transfer.quantity_requested }}</span>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <UBadge color="yellow">{{ $t('transfers.approved') }}</UBadge>
                <UIcon name="i-heroicons-chevron-right" class="text-gray-400" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Picking -->
      <div v-if="activePicking" class="bg-white rounded-lg border">
        <div class="px-4 py-3 border-b bg-blue-50">
          <h3 class="font-medium text-blue-900">{{ $t('warehouse.picking') }}: {{ activePicking.transfer_number }}</h3>
          <p class="text-sm text-blue-700">{{ activePicking.product.name }}</p>
        </div>
        
        <div class="p-4 space-y-4">
          <!-- Location guidance -->
          <div class="bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">{{ $t('warehouse.pick_from_location') }}:</p>
            <p class="font-medium text-lg">{{ suggestedLocation }}</p>
          </div>
          
          <!-- Quantity input -->
          <div>
            <UFormGroup :label="$t('warehouse.quantity_to_pick')" required>
              <UInput 
                v-model="pickQuantity" 
                type="number" 
                :max="activePicking.quantity_requested"
                min="1"
                size="lg"
              />
            </UFormGroup>
            <p class="text-sm text-gray-500 mt-1">
              {{ $t('warehouse.requested') }}: {{ activePicking.quantity_requested }}
            </p>
          </div>
          
          <!-- Location scan -->
          <div>
            <UFormGroup :label="$t('warehouse.scan_enter_location')">
              <div class="flex space-x-2">
                <UInput 
                  v-model="scannedLocation" 
                  :placeholder="$t('warehouse.location_code')"
                  class="flex-1"
                />
                <UButton @click="showScanner = true">
                  <UIcon name="i-heroicons-camera" />
                </UButton>
              </div>
            </UFormGroup>
          </div>
          
          <!-- Actions -->
          <div class="flex space-x-3">
            <UButton 
              block 
              :loading="processing"
              @click="completePicking"
            >
              {{ $t('warehouse.complete_picking') }}
            </UButton>
            <UButton variant="outline" @click="cancelPicking">
              {{ $t('common.cancel') }}
            </UButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Cycle Count Mode -->
    <div v-if="mode === 'cycle_count'" class="p-4">
      <!-- Active Cycle Counts -->
      <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('warehouse.active_cycle_counts') }}</h2>
        
        <div v-if="activeCycleCounts.length === 0" class="text-center py-8 text-gray-500">
          {{ $t('warehouse.no_active_cycle_counts') }}
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="cycleCount in activeCycleCounts"
            :key="cycleCount.id"
            class="bg-white rounded-lg border p-4 cursor-pointer hover:bg-gray-50"
            @click="startCycleCounting(cycleCount)"
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium text-gray-900">{{ cycleCount.count_number }}</h3>
                <p class="text-sm text-gray-600">{{ cycleCount.type }} count</p>
                <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                  <span>{{ $t('warehouse.items') }}: {{ cycleCount.total_items }}</span>
                  <span>{{ $t('warehouse.progress') }}: {{ Math.round(cycleCount.completion_percentage) }}%</span>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <UBadge 
                  :color="cycleCount.status === 'in_progress' ? 'blue' : 'gray'"
                >
                  {{ cycleCount.status }}
                </UBadge>
                <UIcon name="i-heroicons-chevron-right" class="text-gray-400" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Counting -->
      <div v-if="activeCounting" class="bg-white rounded-lg border">
        <div class="px-4 py-3 border-b bg-green-50">
          <h3 class="font-medium text-green-900">{{ $t('warehouse.counting') }}: {{ activeCounting.count_number }}</h3>
          <p class="text-sm text-green-700">
            {{ currentItem?.item_name || 'Loading...' }}
          </p>
        </div>
        
        <div class="p-4 space-y-4">
          <!-- Item info -->
          <div v-if="currentItem" class="bg-gray-50 p-3 rounded-lg">
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="font-medium">{{ currentItem.item_name }}</p>
                <p class="text-sm text-gray-600">{{ currentItem.item_sku }}</p>
              </div>
              <UBadge>{{ currentItemIndex + 1 }}/{{ countingItems.length }}</UBadge>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-600">{{ $t('warehouse.location') }}</p>
                <p class="font-medium">{{ currentItem.location }}</p>
              </div>
              <div>
                <p class="text-gray-600">{{ $t('warehouse.expected') }}</p>
                <p class="font-medium">{{ currentItem.expected_quantity }}</p>
              </div>
            </div>
          </div>
          
          <!-- Count input -->
          <div>
            <UFormGroup :label="$t('warehouse.actual_count')" required>
              <UInput 
                v-model="countedQuantity" 
                type="number" 
                min="0"
                size="lg"
                :placeholder="$t('warehouse.enter_actual_count')"
              />
            </UFormGroup>
          </div>
          
          <!-- Notes -->
          <div>
            <UFormGroup :label="$t('warehouse.notes_optional')">
              <UTextarea 
                v-model="countNotes" 
                :placeholder="$t('warehouse.discrepancies_placeholder')"
                rows="2"
              />
            </UFormGroup>
          </div>
          
          <!-- Actions -->
          <div class="flex space-x-3">
            <UButton 
              block 
              :loading="processing"
              @click="recordCount"
            >
              {{ $t('warehouse.record_count') }}
            </UButton>
            <UButton variant="outline" @click="skipItem">
              {{ $t('warehouse.skip_item') }}
            </UButton>
          </div>
          
          <!-- Navigation -->
          <div class="flex space-x-3">
            <UButton 
              variant="soft" 
              :disabled="currentItemIndex === 0"
              @click="previousItem"
            >
              <UIcon name="i-heroicons-chevron-left" class="mr-1" />
              {{ $t('common.previous') }}
            </UButton>
            <UButton 
              variant="soft" 
              :disabled="currentItemIndex === countingItems.length - 1"
              @click="nextItem"
            >
              {{ $t('common.next') }}
              <UIcon name="i-heroicons-chevron-right" class="ml-1" />
            </UButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <UModal v-model="showScanner">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">
            {{ mode === 'transfer' ? $t('warehouse.scan_location') : $t('warehouse.scan_product_location') }}
          </h3>
          <UButton variant="ghost" @click="showScanner = false">
            <UIcon name="i-heroicons-x-mark" />
          </UButton>
        </div>
        
        <BarcodeScanner 
          :mode="mode === 'transfer' ? 'location' : 'product'"
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
        <h3 class="text-lg font-semibold mb-2">{{ successMessage }}</h3>
        <UButton @click="showSuccess = false">{{ $t('common.continue') }}</UButton>
      </div>
    </UModal>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

const { getDashboard } = useAPI()

const mode = ref('transfer')
const showScanner = ref(false)
const showSuccess = ref(false)
const processing = ref(false)
const successMessage = ref('')

// Transfer picking
const pendingTransfers = ref([])
const activePicking = ref(null)
const pickQuantity = ref(null)
const scannedLocation = ref('')
const suggestedLocation = ref('A1-B2-S3')

// Cycle counting
const activeCycleCounts = ref([])
const activeCounting = ref(null)
const countingItems = ref([])
const currentItemIndex = ref(0)
const countedQuantity = ref(null)
const countNotes = ref('')

const { t } = useI18n()

const currentMode = computed(() => {
  return mode.value === 'transfer' ? 'Transfer' : 'Cycle Count'
})

const currentModeText = computed(() => {
  return mode.value === 'transfer' ? t('warehouse.transfer') : t('warehouse.cycle_count')
})

const currentItem = computed(() => {
  if (!countingItems.value.length || currentItemIndex.value >= countingItems.value.length) {
    return null
  }
  return countingItems.value[currentItemIndex.value]
})

// Methods
const setMode = (newMode) => {
  mode.value = newMode
  if (newMode === 'transfer') {
    loadPendingTransfers()
  } else {
    loadActiveCycleCounts()
  }
}

const toggleMode = () => {
  setMode(mode.value === 'transfer' ? 'cycle_count' : 'transfer')
}

const loadPendingTransfers = async () => {
  try {
    // Mock data for now
    pendingTransfers.value = [
      {
        id: 1,
        transfer_number: 'TRF-20241201-0001',
        product: { name: 'Blue Jeans - Size 32' },
        from_store: { name: 'Warehouse' },
        to_store: { name: 'Store A' },
        quantity_requested: 10
      },
      {
        id: 2,
        transfer_number: 'TRF-20241201-0002',
        product: { name: 'Black Chinos - Size 34' },
        from_store: { name: 'Warehouse' },
        to_store: { name: 'Store B' },
        quantity_requested: 5
      }
    ]
  } catch (error) {
    console.error('Failed to load transfers:', error)
  }
}

const loadActiveCycleCounts = async () => {
  try {
    // Mock data for now
    activeCycleCounts.value = [
      {
        id: 1,
        count_number: 'CC-20241201-001',
        type: 'partial',
        status: 'in_progress',
        total_items: 25,
        completion_percentage: 40
      }
    ]
  } catch (error) {
    console.error('Failed to load cycle counts:', error)
  }
}

const startTransferPicking = (transfer) => {
  activePicking.value = transfer
  pickQuantity.value = transfer.quantity_requested
}

const startCycleCounting = async (cycleCount) => {
  activeCounting.value = cycleCount
  currentItemIndex.value = 0
  
  // Load counting items - mock data for now
  countingItems.value = [
    {
      id: 1,
      item_name: 'Blue Jeans - Size 32',
      item_sku: 'BJ-32-001',
      location: 'A1-B2-S3',
      expected_quantity: 25,
      status: 'pending'
    },
    {
      id: 2,
      item_name: 'Red T-Shirt - Size M',
      item_sku: 'RT-M-002',
      location: 'A2-B1-S5',
      expected_quantity: 15,
      status: 'pending'
    }
  ]
}

const completePicking = async () => {
  if (!pickQuantity.value || !scannedLocation.value) {
    // Show validation error
    return
  }
  
  processing.value = true
  
  try {
    // API call to complete picking
    // await shipTransfer(activePicking.value.id, {
    //   quantity_shipped: pickQuantity.value,
    //   location: scannedLocation.value
    // })
    
    successMessage.value = `Transfer ${activePicking.value.transfer_number} picked successfully!`
    showSuccess.value = true
    
    // Reset
    activePicking.value = null
    pickQuantity.value = null
    scannedLocation.value = ''
    
    // Reload transfers
    await loadPendingTransfers()
    
  } catch (error) {
    console.error('Picking failed:', error)
  } finally {
    processing.value = false
  }
}

const cancelPicking = () => {
  activePicking.value = null
  pickQuantity.value = null
  scannedLocation.value = ''
}

const recordCount = async () => {
  if (countedQuantity.value === null) {
    // Show validation error
    return
  }
  
  processing.value = true
  
  try {
    const item = currentItem.value
    // await recordCycleCountItem(item.id, {
    //   counted_quantity: countedQuantity.value,
    //   notes: countNotes.value
    // })
    
    // Mark as counted
    countingItems.value[currentItemIndex.value].status = 'counted'
    countingItems.value[currentItemIndex.value].counted_quantity = countedQuantity.value
    
    // Move to next item
    if (currentItemIndex.value < countingItems.value.length - 1) {
      nextItem()
    } else {
      successMessage.value = 'Cycle count completed!'
      showSuccess.value = true
      activeCounting.value = null
    }
    
  } catch (error) {
    console.error('Failed to record count:', error)
  } finally {
    processing.value = false
  }
}

const skipItem = () => {
  if (currentItemIndex.value < countingItems.value.length - 1) {
    nextItem()
  }
}

const nextItem = () => {
  if (currentItemIndex.value < countingItems.value.length - 1) {
    currentItemIndex.value++
    countedQuantity.value = null
    countNotes.value = ''
  }
}

const previousItem = () => {
  if (currentItemIndex.value > 0) {
    currentItemIndex.value--
    countedQuantity.value = null
    countNotes.value = ''
  }
}

const handleScanResult = (scanResult) => {
  showScanner.value = false
  
  if (mode.value === 'transfer') {
    scannedLocation.value = scanResult.code
  } else {
    // Handle product/location scan for cycle count
    // Could auto-find item or verify location
  }
}

// Lifecycle
onMounted(() => {
  setMode('transfer')
})
</script>