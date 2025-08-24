<template>
  <div class="barcode-scanner">
    <!-- Camera View -->
    <div v-if="isScanning" class="relative bg-black rounded-lg overflow-hidden">
      <video ref="videoElement" class="w-full h-64 object-cover" autoplay muted></video>
      
      <!-- Scanning overlay -->
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="border-2 border-red-500 w-64 h-16 rounded-lg opacity-50"></div>
      </div>
      
      <!-- Controls -->
      <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-4">
        <UButton @click="stopScanning" color="red" variant="solid">
          <UIcon name="i-heroicons-x-mark" class="mr-2" />
          Stop
        </UButton>
        <UButton @click="toggleFlash" variant="outline" color="white">
          <UIcon :name="flashOn ? 'i-heroicons-bolt-slash' : 'i-heroicons-bolt'" class="mr-2" />
          Flash
        </UButton>
      </div>
    </div>
    
    <!-- Start scanning button -->
    <div v-else class="text-center p-8 bg-gray-100 rounded-lg">
      <UIcon name="i-heroicons-camera" class="h-16 w-16 text-gray-400 mx-auto mb-4" />
      <p class="text-gray-600 mb-4">{{ mode === 'product' ? 'Scan product barcode' : 'Scan location barcode' }}</p>
      <UButton @click="startScanning" size="lg">
        <UIcon name="i-heroicons-camera" class="mr-2" />
        Start Scanner
      </UButton>
    </div>
    
    <!-- Manual input fallback -->
    <div class="mt-4">
      <UFormGroup label="Or enter manually">
        <div class="flex space-x-2">
          <UInput 
            v-model="manualInput" 
            :placeholder="mode === 'product' ? 'Enter SKU or barcode' : 'Enter location code'"
            class="flex-1"
          />
          <UButton @click="handleManualInput">
            <UIcon name="i-heroicons-magnifying-glass" />
          </UButton>
        </div>
      </UFormGroup>
    </div>
    
    <!-- Recent scans -->
    <div v-if="recentScans.length > 0" class="mt-4">
      <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Scans</h4>
      <div class="space-y-2">
        <div 
          v-for="scan in recentScans" 
          :key="scan.code"
          class="flex items-center justify-between p-2 bg-gray-50 rounded cursor-pointer hover:bg-gray-100"
          @click="handleScanResult(scan.code)"
        >
          <span class="text-sm">{{ scan.code }}</span>
          <span class="text-xs text-gray-500">{{ formatTime(scan.timestamp) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Quagga from 'quagga'

const props = defineProps({
  mode: {
    type: String,
    default: 'product', // 'product' or 'location'
    validator: (value) => ['product', 'location'].includes(value)
  }
})

const emit = defineEmits(['scan-result'])

const videoElement = ref(null)
const isScanning = ref(false)
const flashOn = ref(false)
const manualInput = ref('')
const recentScans = ref([])

let currentStream = null

const startScanning = async () => {
  try {
    // Request camera permission
    currentStream = await navigator.mediaDevices.getUserMedia({
      video: { 
        facingMode: 'environment', // Use back camera
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    })
    
    if (videoElement.value) {
      videoElement.value.srcObject = currentStream
    }
    
    isScanning.value = true
    
    // Wait for video to be ready
    await nextTick()
    
    // Initialize Quagga
    Quagga.init({
      inputStream: {
        name: 'Live',
        type: 'LiveStream',
        target: videoElement.value,
        constraints: {
          width: 640,
          height: 480,
          facingMode: 'environment'
        }
      },
      locator: {
        patchSize: 'medium',
        halfSample: true
      },
      numOfWorkers: 2,
      decoder: {
        readers: [
          'code_128_reader',
          'ean_reader',
          'ean_8_reader',
          'code_39_reader',
          'code_39_vin_reader',
          'codabar_reader',
          'upc_reader',
          'upc_e_reader'
        ]
      },
      locate: true
    }, (err) => {
      if (err) {
        console.error('Quagga initialization failed:', err)
        isScanning.value = false
        return
      }
      Quagga.start()
    })
    
    // Listen for successful scans
    Quagga.onDetected(handleBarcodeDetected)
    
  } catch (error) {
    console.error('Camera access denied:', error)
    // Show user-friendly error
    alert('Camera access is required for barcode scanning. Please enable camera permissions.')
  }
}

const stopScanning = () => {
  if (currentStream) {
    currentStream.getTracks().forEach(track => track.stop())
    currentStream = null
  }
  
  if (isScanning.value) {
    Quagga.stop()
  }
  
  isScanning.value = false
}

const toggleFlash = async () => {
  if (currentStream) {
    const videoTrack = currentStream.getVideoTracks()[0]
    const capabilities = videoTrack.getCapabilities()
    
    if (capabilities.torch) {
      await videoTrack.applyConstraints({
        advanced: [{ torch: !flashOn.value }]
      })
      flashOn.value = !flashOn.value
    }
  }
}

const handleBarcodeDetected = (result) => {
  const code = result.codeResult.code
  
  // Add to recent scans
  addToRecentScans(code)
  
  // Stop scanning after successful read
  stopScanning()
  
  // Emit result
  handleScanResult(code)
}

const handleScanResult = (code) => {
  emit('scan-result', {
    code,
    mode: props.mode,
    timestamp: new Date()
  })
}

const handleManualInput = () => {
  if (manualInput.value.trim()) {
    handleScanResult(manualInput.value.trim())
    manualInput.value = ''
  }
}

const addToRecentScans = (code) => {
  // Remove if already exists
  recentScans.value = recentScans.value.filter(scan => scan.code !== code)
  
  // Add to beginning
  recentScans.value.unshift({
    code,
    timestamp: new Date()
  })
  
  // Keep only last 5 scans
  recentScans.value = recentScans.value.slice(0, 5)
  
  // Save to localStorage
  localStorage.setItem('recentScans', JSON.stringify(recentScans.value))
}

const loadRecentScans = () => {
  const saved = localStorage.getItem('recentScans')
  if (saved) {
    recentScans.value = JSON.parse(saved).map(scan => ({
      ...scan,
      timestamp: new Date(scan.timestamp)
    }))
  }
}

const formatTime = (timestamp) => {
  return timestamp.toLocaleTimeString()
}

// Lifecycle
onMounted(() => {
  loadRecentScans()
})

onUnmounted(() => {
  stopScanning()
})

// Watch for route changes to stop scanning
watch(() => useRoute().path, () => {
  stopScanning()
})
</script>

<style scoped>
.barcode-scanner video {
  transform: scaleX(-1); /* Mirror video for better UX */
}
</style>