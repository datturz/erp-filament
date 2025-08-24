interface OfflineAction {
  id: string
  type: string
  data: any
  timestamp: Date
  retryCount: number
}

interface SyncStatus {
  isOnline: boolean
  pendingActions: number
  lastSync: Date | null
  syncInProgress: boolean
}

export const useOfflineSync = () => {
  const { $fetch } = useNuxtApp()
  const isOnline = useOnline()
  const pendingActions = ref<OfflineAction[]>([])
  const syncStatus = ref<SyncStatus>({
    isOnline: isOnline.value,
    pendingActions: 0,
    lastSync: null,
    syncInProgress: false
  })
  
  const config = useRuntimeConfig()
  const { token } = useAuth()
  
  // Load pending actions from localStorage
  const loadPendingActions = () => {
    const stored = localStorage.getItem('offlineActions')
    if (stored) {
      pendingActions.value = JSON.parse(stored).map((action: any) => ({
        ...action,
        timestamp: new Date(action.timestamp)
      }))
    }
    updateSyncStatus()
  }
  
  // Save pending actions to localStorage
  const savePendingActions = () => {
    localStorage.setItem('offlineActions', JSON.stringify(pendingActions.value))
    updateSyncStatus()
  }
  
  // Add action to offline queue
  const queueAction = (type: string, data: any) => {
    const action: OfflineAction = {
      id: generateId(),
      type,
      data,
      timestamp: new Date(),
      retryCount: 0
    }
    
    pendingActions.value.push(action)
    savePendingActions()
    
    // Try to sync immediately if online
    if (isOnline.value) {
      syncActions()
    }
    
    return action.id
  }
  
  // Execute API call with offline fallback
  const executeWithOfflineSupport = async <T>(
    endpoint: string,
    options: any = {},
    fallbackData?: any
  ): Promise<T> => {
    if (!isOnline.value) {
      // Queue for later sync
      queueAction('api_call', { endpoint, options })
      
      // Return fallback data or throw error
      if (fallbackData) {
        return fallbackData
      }
      throw new Error('Offline - action queued for sync')
    }
    
    try {
      return await apiCall<T>(endpoint, options)
    } catch (error) {
      // If request failed, queue it
      queueAction('api_call', { endpoint, options })
      throw error
    }
  }
  
  // Sync pending actions
  const syncActions = async () => {
    if (!isOnline.value || syncStatus.value.syncInProgress) {
      return
    }
    
    syncStatus.value.syncInProgress = true
    const actionsToSync = [...pendingActions.value]
    
    for (const action of actionsToSync) {
      try {
        await syncSingleAction(action)
        // Remove successful action
        pendingActions.value = pendingActions.value.filter(a => a.id !== action.id)
      } catch (error) {
        // Increment retry count
        const actionIndex = pendingActions.value.findIndex(a => a.id === action.id)
        if (actionIndex >= 0) {
          pendingActions.value[actionIndex].retryCount++
          
          // Remove action if too many retries
          if (pendingActions.value[actionIndex].retryCount > 5) {
            pendingActions.value.splice(actionIndex, 1)
          }
        }
        
        console.error('Sync failed for action:', action, error)
      }
    }
    
    syncStatus.value.syncInProgress = false
    syncStatus.value.lastSync = new Date()
    savePendingActions()
  }
  
  // Sync a single action
  const syncSingleAction = async (action: OfflineAction) => {
    switch (action.type) {
      case 'api_call':
        return await apiCall(action.data.endpoint, action.data.options)
      case 'sale_transaction':
        return await syncSaleTransaction(action.data)
      case 'stock_adjustment':
        return await syncStockAdjustment(action.data)
      case 'transfer_request':
        return await syncTransferRequest(action.data)
      case 'batch_update':
        return await syncBatchUpdate(action.data)
      default:
        throw new Error(`Unknown action type: ${action.type}`)
    }
  }
  
  // Specific sync handlers
  const syncSaleTransaction = async (saleData: any) => {
    return await apiCall('/sales/process', {
      method: 'POST',
      body: {
        ...saleData,
        offline_timestamp: saleData.timestamp
      }
    })
  }
  
  const syncStockAdjustment = async (adjustmentData: any) => {
    return await apiCall('/inventory/adjust', {
      method: 'POST',
      body: adjustmentData
    })
  }
  
  const syncTransferRequest = async (transferData: any) => {
    return await apiCall('/transfers', {
      method: 'POST',
      body: transferData
    })
  }
  
  const syncBatchUpdate = async (batchData: any) => {
    return await apiCall(`/batches/${batchData.id}/progress`, {
      method: 'PUT',
      body: batchData
    })
  }
  
  // Queue specific actions
  const queueSale = (saleData: any) => {
    return queueAction('sale_transaction', saleData)
  }
  
  const queueStockAdjustment = (adjustmentData: any) => {
    return queueAction('stock_adjustment', adjustmentData)
  }
  
  const queueTransferRequest = (transferData: any) => {
    return queueAction('transfer_request', transferData)
  }
  
  const queueBatchUpdate = (batchData: any) => {
    return queueAction('batch_update', batchData)
  }
  
  // Clear all pending actions
  const clearPendingActions = () => {
    pendingActions.value = []
    savePendingActions()
  }
  
  // Update sync status
  const updateSyncStatus = () => {
    syncStatus.value.isOnline = isOnline.value
    syncStatus.value.pendingActions = pendingActions.value.length
  }
  
  // API call helper
  const apiCall = async <T>(endpoint: string, options: any = {}) => {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
    
    if (token.value) {
      headers.Authorization = `Bearer ${token.value}`
    }
    
    return await $fetch<T>(endpoint, {
      baseURL: config.public.apiBase,
      headers,
      ...options
    })
  }
  
  // Generate unique ID
  const generateId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2)
  }
  
  // Watch online status
  watch(isOnline, (online) => {
    updateSyncStatus()
    if (online) {
      // Auto-sync when coming back online
      setTimeout(syncActions, 1000)
    }
  })
  
  // Auto-sync interval when online
  const syncInterval = setInterval(() => {
    if (isOnline.value && pendingActions.value.length > 0) {
      syncActions()
    }
  }, 30000) // Sync every 30 seconds
  
  // Lifecycle
  onMounted(() => {
    loadPendingActions()
    updateSyncStatus()
  })
  
  onUnmounted(() => {
    clearInterval(syncInterval)
  })
  
  return {
    // State
    syncStatus: readonly(syncStatus),
    pendingActions: readonly(pendingActions),
    isOnline,
    
    // Methods
    executeWithOfflineSupport,
    queueAction,
    queueSale,
    queueStockAdjustment,
    queueTransferRequest,
    queueBatchUpdate,
    syncActions,
    clearPendingActions,
    
    // Status
    hasPendingActions: computed(() => pendingActions.value.length > 0),
    canSync: computed(() => isOnline.value && !syncStatus.value.syncInProgress)
  }
}