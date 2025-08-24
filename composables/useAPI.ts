export const useAPI = () => {
  const { token } = useAuth()
  const config = useRuntimeConfig()
  
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
  
  // Dashboard
  const getDashboard = () => apiCall('/dashboard')
  
  // Products
  const searchProducts = (query: string) => 
    apiCall(`/products/search?q=${encodeURIComponent(query)}`)
  
  const checkStock = (query: string) => 
    apiCall(`/stock/check?q=${encodeURIComponent(query)}`)
  
  // Sales
  const processSale = (saleData: any) => 
    apiCall('/sales/process', { method: 'POST', body: saleData })
  
  // Production
  const getBatches = () => apiCall('/batches')
  
  const updateBatchProgress = (batchId: number, progressData: any) =>
    apiCall(`/batches/${batchId}/progress`, { method: 'PUT', body: progressData })
  
  // Inventory
  const adjustInventory = (adjustmentData: any) =>
    apiCall('/inventory/adjust', { method: 'POST', body: adjustmentData })
  
  return {
    getDashboard,
    searchProducts,
    checkStock,
    processSale,
    getBatches,
    updateBatchProgress,
    adjustInventory
  }
}