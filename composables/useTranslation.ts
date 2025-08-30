// Simple translation composable
export const useTranslation = () => {
  const translations = {
    'auth.login': 'Login',
    'auth.email': 'Email',
    'auth.password': 'Password',
    'auth.remember_me': 'Remember me',
    'auth.fill_all_fields': 'Please fill all fields',
    'auth.login_failed': 'Login failed',
    'dashboard.title': 'Dashboard',
    'pos.title': 'Point of Sale',
    'dashboard.start_new_sale': 'Start New Sale',
    'dashboard.view_sales': 'View Sales',
    'dashboard.warehouse_operations': 'Warehouse Operations',
    'nav.inventory': 'Inventory',
    'nav.production': 'Production',
    'nav.transfers': 'Transfers',
    'warehouse.cycle_count': 'Cycle Count',
    'dashboard.recent_activity': 'Recent Activity',
    'dashboard.alerts': 'Alerts'
  }
  
  const $t = (key: string) => {
    return translations[key] || key
  }
  
  return { $t }
}