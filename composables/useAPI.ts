export const useAPI = () => {
  const { token } = useAuth()
  const config = useRuntimeConfig()
  const baseURL = 'https://jubilant-prosperity-production.up.railway.app'
  
  const apiCall = async <T>(endpoint: string, options: any = {}) => {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
    
    if (token.value) {
      headers.Authorization = `Bearer ${token.value}`
    }
    
    try {
      return await $fetch<T>(endpoint, {
        baseURL,
        headers,
        ...options
      })
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }
  
  // Dashboard
  const getDashboard = () => apiCall('/api.php/v1/dashboard')
  
  // Products CRUD
  const getProducts = () => apiCall('/api.php/v1/products')
  const getProduct = (id: string) => apiCall(`/api.php/v1/products/${id}`)
  const createProduct = (data: any) => apiCall('/api.php/v1/products', { method: 'POST', body: data })
  const updateProduct = (id: string, data: any) => apiCall(`/api.php/v1/products/${id}`, { method: 'PUT', body: data })
  const deleteProduct = (id: string) => apiCall(`/api.php/v1/products/${id}`, { method: 'DELETE' })
  const searchProducts = (query: string) => apiCall(`/api.php/v1/products/search?q=${encodeURIComponent(query)}`)
  
  // Categories CRUD
  const getCategories = () => apiCall('/api.php/v1/categories')
  const getCategory = (id: string) => apiCall(`/api.php/v1/categories/${id}`)
  const createCategory = (data: any) => apiCall('/api.php/v1/categories', { method: 'POST', body: data })
  const updateCategory = (id: string, data: any) => apiCall(`/api.php/v1/categories/${id}`, { method: 'PUT', body: data })
  const deleteCategory = (id: string) => apiCall(`/api.php/v1/categories/${id}`, { method: 'DELETE' })
  
  // Customers CRUD
  const getCustomers = () => apiCall('/api.php/v1/customers')
  const getCustomer = (id: string) => apiCall(`/api.php/v1/customers/${id}`)
  const createCustomer = (data: any) => apiCall('/api.php/v1/customers', { method: 'POST', body: data })
  const updateCustomer = (id: string, data: any) => apiCall(`/api.php/v1/customers/${id}`, { method: 'PUT', body: data })
  const deleteCustomer = (id: string) => apiCall(`/api.php/v1/customers/${id}`, { method: 'DELETE' })
  
  // Suppliers CRUD
  const getSuppliers = () => apiCall('/api.php/v1/suppliers')
  const getSupplier = (id: string) => apiCall(`/api.php/v1/suppliers/${id}`)
  const createSupplier = (data: any) => apiCall('/api.php/v1/suppliers', { method: 'POST', body: data })
  const updateSupplier = (id: string, data: any) => apiCall(`/api.php/v1/suppliers/${id}`, { method: 'PUT', body: data })
  const deleteSupplier = (id: string) => apiCall(`/api.php/v1/suppliers/${id}`, { method: 'DELETE' })
  
  // Sales
  const getSales = () => apiCall('/api.php/v1/sales')
  const getSale = (id: string) => apiCall(`/api.php/v1/sales/${id}`)
  const processSale = (saleData: any) => apiCall('/api.php/v1/sales', { method: 'POST', body: saleData })
  
  // Inventory
  const getInventory = () => apiCall('/api.php/v1/inventory')
  const checkStock = (productId: string) => apiCall(`/api.php/v1/inventory/check/${productId}`)
  const adjustInventory = (adjustmentData: any) => apiCall('/api.php/v1/inventory/adjust', { method: 'POST', body: adjustmentData })
  
  // Production/Batches
  const getBatches = () => apiCall('/api.php/v1/batches')
  const getBatch = (id: string) => apiCall(`/api.php/v1/batches/${id}`)
  const createBatch = (data: any) => apiCall('/api.php/v1/batches', { method: 'POST', body: data })
  const updateBatchProgress = (batchId: string, progressData: any) => apiCall(`/api.php/v1/batches/${batchId}/progress`, { method: 'PUT', body: progressData })
  
  // Income/Expenses
  const getIncome = () => apiCall('/api.php/v1/income')
  const createIncome = (data: any) => apiCall('/api.php/v1/income', { method: 'POST', body: data })
  const getExpenses = () => apiCall('/api.php/v1/expenses')
  const createExpense = (data: any) => apiCall('/api.php/v1/expenses', { method: 'POST', body: data })
  
  // Incoming/Outgoing Goods
  const getIncomingGoods = () => apiCall('/api.php/v1/incoming')
  const createIncomingGoods = (data: any) => apiCall('/api.php/v1/incoming', { method: 'POST', body: data })
  const getOutgoingGoods = () => apiCall('/api.php/v1/outgoing')
  const createOutgoingGoods = (data: any) => apiCall('/api.php/v1/outgoing', { method: 'POST', body: data })
  
  // Reports
  const getSalesReport = (params?: any) => apiCall('/api.php/v1/reports/sales', { params })
  const getInventoryReport = (params?: any) => apiCall('/api.php/v1/reports/inventory', { params })
  const getFinancialReport = (params?: any) => apiCall('/api.php/v1/reports/financial', { params })
  const getProductionReport = (params?: any) => apiCall('/api.php/v1/reports/production', { params })
  
  return {
    getDashboard,
    // Products
    getProducts,
    getProduct,
    createProduct,
    updateProduct,
    deleteProduct,
    searchProducts,
    // Categories
    getCategories,
    getCategory,
    createCategory,
    updateCategory,
    deleteCategory,
    // Customers
    getCustomers,
    getCustomer,
    createCustomer,
    updateCustomer,
    deleteCustomer,
    // Suppliers
    getSuppliers,
    getSupplier,
    createSupplier,
    updateSupplier,
    deleteSupplier,
    // Sales
    getSales,
    getSale,
    processSale,
    // Inventory
    getInventory,
    checkStock,
    adjustInventory,
    // Production
    getBatches,
    getBatch,
    createBatch,
    updateBatchProgress,
    // Finance
    getIncome,
    createIncome,
    getExpenses,
    createExpense,
    // Warehouse
    getIncomingGoods,
    createIncomingGoods,
    getOutgoingGoods,
    createOutgoingGoods,
    // Reports
    getSalesReport,
    getInventoryReport,
    getFinancialReport,
    getProductionReport
  }
}