interface User {
  id: number
  name: string
  email: string
  role: string
  store?: string
}

interface LoginCredentials {
  email: string
  password: string
}

export const useAuth = () => {
  const user = ref<User | null>(null)
  const token = useCookie('auth-token', { default: () => null })
  const isLoggedIn = computed(() => !!token.value)
  
  const config = useRuntimeConfig()
  
  const login = async (credentials: LoginCredentials) => {
    try {
      const { data } = await $fetch<{ user: User, token: string }>('/auth/login', {
        baseURL: config.public.apiBase,
        method: 'POST',
        body: credentials
      })
      
      token.value = data.token
      user.value = data.user
      
      await navigateTo('/dashboard')
    } catch (error) {
      throw error
    }
  }
  
  const logout = async () => {
    try {
      await $fetch('/auth/logout', {
        baseURL: config.public.apiBase,
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
    } catch (error) {
      // Continue logout even if API call fails
    }
    
    token.value = null
    user.value = null
    await navigateTo('/login')
  }
  
  const getUser = async () => {
    if (!token.value) return null
    
    try {
      const userData = await $fetch<User>('/user', {
        baseURL: config.public.apiBase,
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      
      user.value = userData
      return userData
    } catch (error) {
      token.value = null
      user.value = null
      return null
    }
  }
  
  const hasRole = (roles: string | string[]) => {
    if (!user.value) return false
    const userRoles = Array.isArray(roles) ? roles : [roles]
    return userRoles.includes(user.value.role)
  }
  
  const canAccessPOS = () => hasRole(['store_manager', 'store_associate'])
  const canAccessWarehouse = () => hasRole(['warehouse_manager', 'warehouse_worker', 'production_supervisor'])
  const canManageInventory = () => hasRole(['warehouse_manager', 'store_manager'])
  
  return {
    user: readonly(user),
    token: readonly(token),
    isLoggedIn,
    login,
    logout,
    getUser,
    hasRole,
    canAccessPOS,
    canAccessWarehouse,
    canManageInventory
  }
}