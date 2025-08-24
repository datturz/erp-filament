<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-3">
        <div>
          <h1 class="text-xl font-semibold text-gray-900">{{ $t('dashboard.title') }}</h1>
          <p class="text-sm text-gray-500">{{ user?.store || 'System' }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <LanguageSelector />
          <UButton variant="ghost" size="sm" @click="refreshData">
            <UIcon name="i-heroicons-arrow-path" :class="{ 'animate-spin': loading }" />
          </UButton>
          <UButton variant="ghost" size="sm" @click="logout">
            <UIcon name="i-heroicons-arrow-right-on-rectangle" />
          </UButton>
        </div>
      </div>
    </header>

    <!-- Quick Stats -->
    <div class="p-4">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div 
          v-for="stat in stats" 
          :key="stat.label"
          class="bg-white p-4 rounded-lg shadow-sm"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UIcon :name="stat.icon" class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-3">
              <p class="text-sm text-gray-500">{{ stat.label }}</p>
              <p class="text-lg font-semibold text-gray-900">{{ stat.value }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Role-based sections -->
      <div class="space-y-6">
        <!-- POS Section -->
        <div v-if="canAccessPOS()" class="bg-white rounded-lg shadow-sm">
          <div class="px-4 py-3 border-b">
            <h2 class="text-lg font-medium text-gray-900">{{ $t('pos.title') }}</h2>
          </div>
          <div class="p-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
              <UButton size="lg" block @click="navigateTo('/pos')">
                <UIcon name="i-heroicons-credit-card" class="mr-2" />
                {{ $t('dashboard.start_new_sale') }}
              </UButton>
              <UButton variant="outline" size="lg" block @click="navigateTo('/sales')">
                <UIcon name="i-heroicons-chart-bar" class="mr-2" />
                {{ $t('dashboard.view_sales') }}
              </UButton>
            </div>
          </div>
        </div>

        <!-- Warehouse Section -->
        <div v-if="canAccessWarehouse()" class="bg-white rounded-lg shadow-sm">
          <div class="px-4 py-3 border-b">
            <h2 class="text-lg font-medium text-gray-900">{{ $t('dashboard.warehouse_operations') }}</h2>
          </div>
          <div class="p-4">
            <div class="grid grid-cols-2 gap-4">
              <UButton variant="outline" size="lg" block @click="navigateTo('/inventory')">
                <UIcon name="i-heroicons-cube" class="mr-2" />
                {{ $t('nav.inventory') }}
              </UButton>
              <UButton variant="outline" size="lg" block @click="navigateTo('/batches')">
                <UIcon name="i-heroicons-cog" class="mr-2" />
                {{ $t('nav.production') }}
              </UButton>
              <UButton variant="outline" size="lg" block @click="navigateTo('/transfers')">
                <UIcon name="i-heroicons-arrow-path" class="mr-2" />
                {{ $t('nav.transfers') }}
              </UButton>
              <UButton variant="outline" size="lg" block @click="navigateTo('/cycle-count')">
                <UIcon name="i-heroicons-clipboard-document-list" class="mr-2" />
                {{ $t('warehouse.cycle_count') }}
              </UButton>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm">
          <div class="px-4 py-3 border-b">
            <h2 class="text-lg font-medium text-gray-900">{{ $t('dashboard.recent_activity') }}</h2>
          </div>
          <div class="divide-y">
            <div 
              v-for="activity in recentActivity" 
              :key="activity.id"
              class="px-4 py-3 flex items-center space-x-3"
            >
              <div class="flex-shrink-0">
                <div class="w-2 h-2 rounded-full" :class="getActivityColor(activity.type)"></div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900">{{ activity.description }}</p>
                <p class="text-xs text-gray-500">{{ formatTime(activity.timestamp) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerts -->
        <div v-if="alerts.length > 0" class="bg-white rounded-lg shadow-sm">
          <div class="px-4 py-3 border-b">
            <h2 class="text-lg font-medium text-gray-900">{{ $t('dashboard.alerts') }}</h2>
          </div>
          <div class="divide-y">
            <div 
              v-for="alert in alerts" 
              :key="alert.id"
              class="px-4 py-3"
            >
              <UAlert 
                :title="alert.title"
                :description="alert.message"
                :color="alert.type === 'warning' ? 'orange' : 'red'"
                variant="soft"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

const { user, logout, canAccessPOS, canAccessWarehouse } = useAuth()
const { getDashboard } = useAPI()

const loading = ref(false)
const dashboardData = ref({
  stats: {},
  alerts: [],
  recent_activity: []
})

const { t } = useI18n()

const stats = computed(() => [
  {
    label: t('dashboard.today_sales'),
    value: formatCurrency(dashboardData.value.stats?.today_sales || 0),
    icon: 'i-heroicons-currency-dollar'
  },
  {
    label: t('dashboard.transactions'),
    value: dashboardData.value.stats?.today_transactions || 0,
    icon: 'i-heroicons-shopping-cart'
  },
  {
    label: t('dashboard.low_stock'),
    value: dashboardData.value.stats?.low_stock_items || 0,
    icon: 'i-heroicons-exclamation-triangle'
  },
  {
    label: t('dashboard.active_batches'),
    value: dashboardData.value.stats?.active_batches || 0,
    icon: 'i-heroicons-cog'
  }
])

const alerts = computed(() => dashboardData.value.alerts || [])
const recentActivity = computed(() => dashboardData.value.recent_activity || [])

const loadDashboard = async () => {
  loading.value = true
  try {
    const data = await getDashboard()
    dashboardData.value = data
  } catch (error) {
    console.error('Failed to load dashboard:', error)
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  loadDashboard()
}

const getActivityColor = (type) => {
  switch (type) {
    case 'sale': return 'bg-green-500'
    case 'transfer': return 'bg-blue-500'
    case 'production': return 'bg-purple-500'
    case 'adjustment': return 'bg-yellow-500'
    default: return 'bg-gray-500'
  }
}

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString()
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

onMounted(() => {
  loadDashboard()
})
</script>