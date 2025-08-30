const { chromium } = require('playwright');

async function testERPApp() {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  console.log('🔍 Starting ERP Application Test...\n');
  
  try {
    // Test 1: Load Dashboard
    console.log('1. Testing Dashboard...');
    await page.goto('https://erp-filament.vercel.app/dashboard');
    await page.waitForLoadState('networkidle');
    
    const dashboardTitle = await page.textContent('h1');
    console.log(`   ✅ Dashboard loaded: ${dashboardTitle}`);
    
    // Test 2: Check sidebar visibility
    console.log('\n2. Testing Sidebar Navigation...');
    const sidebar = await page.locator('.bg-gray-900');
    const isSidebarVisible = await sidebar.isVisible();
    console.log(`   ${isSidebarVisible ? '✅' : '❌'} Sidebar visible: ${isSidebarVisible}`);
    
    // Test 3: Test each menu item
    const menuItems = [
      { name: 'POS Terminal', url: '/pos', selector: 'a[href="/pos"]' },
      { name: 'Sales History', url: '/sales-history', selector: 'a[href="/sales-history"]' },
      { name: 'Stock Management', url: '/stock-management', selector: 'a[href="/stock-management"]' },
      { name: 'Products', url: '/products', selector: 'a[href="/products"]' },
      { name: 'Categories', url: '/categories', selector: 'a[href="/categories"]' },
      { name: 'Sales Report', url: '/sales-report', selector: 'a[href="/sales-report"]' }
    ];
    
    console.log('\n3. Testing Menu Navigation...');
    for (const item of menuItems) {
      try {
        // Click menu item
        await page.click(item.selector);
        await page.waitForLoadState('networkidle');
        
        // Check URL
        const currentUrl = page.url();
        const isCorrectPage = currentUrl.includes(item.url);
        
        console.log(`   ${isCorrectPage ? '✅' : '❌'} ${item.name}: ${isCorrectPage ? 'Working' : 'Failed'}`);
        
        // Check if page loaded
        await page.waitForTimeout(1000);
        
        // Go back to dashboard for next test
        if (item !== menuItems[menuItems.length - 1]) {
          await page.goto('https://erp-filament.vercel.app/dashboard');
          await page.waitForLoadState('networkidle');
        }
      } catch (error) {
        console.log(`   ❌ ${item.name}: Error - ${error.message}`);
      }
    }
    
    // Test 4: Test POS Terminal functionality
    console.log('\n4. Testing POS Terminal...');
    await page.goto('https://erp-filament.vercel.app/pos');
    await page.waitForLoadState('networkidle');
    
    // Check if product grid is visible
    const productGrid = await page.locator('.grid').first();
    const hasProducts = await productGrid.isVisible();
    console.log(`   ${hasProducts ? '✅' : '❌'} Product grid visible: ${hasProducts}`);
    
    // Test 5: Test Stock Management
    console.log('\n5. Testing Stock Management...');
    await page.goto('https://erp-filament.vercel.app/stock-management');
    await page.waitForLoadState('networkidle');
    
    // Check if stock table is visible
    const stockTable = await page.locator('table').first();
    const hasStockTable = await stockTable.isVisible();
    console.log(`   ${hasStockTable ? '✅' : '❌'} Stock table visible: ${hasStockTable}`);
    
    // Test 6: Test Sales History
    console.log('\n6. Testing Sales History...');
    await page.goto('https://erp-filament.vercel.app/sales-history');
    await page.waitForLoadState('networkidle');
    
    const salesTable = await page.locator('table').first();
    const hasSalesTable = await salesTable.isVisible();
    console.log(`   ${hasSalesTable ? '✅' : '❌'} Sales table visible: ${hasSalesTable}`);
    
    // Test 7: Test Products CRUD
    console.log('\n7. Testing Products Page...');
    await page.goto('https://erp-filament.vercel.app/products');
    await page.waitForLoadState('networkidle');
    
    // Check Add Product button
    const addProductBtn = await page.locator('button:has-text("Add Product")');
    const hasAddButton = await addProductBtn.isVisible();
    console.log(`   ${hasAddButton ? '✅' : '❌'} Add Product button visible: ${hasAddButton}`);
    
    if (hasAddButton) {
      // Click Add Product
      await addProductBtn.click();
      await page.waitForTimeout(500);
      
      // Check if modal opened
      const modal = await page.locator('text=Add New Product');
      const isModalOpen = await modal.isVisible();
      console.log(`   ${isModalOpen ? '✅' : '❌'} Add Product modal opens: ${isModalOpen}`);
      
      // Close modal if open
      if (isModalOpen) {
        const closeBtn = await page.locator('button:has-text("Cancel")');
        if (await closeBtn.isVisible()) {
          await closeBtn.click();
        }
      }
    }
    
    // Test 8: Check responsive menu
    console.log('\n8. Testing Responsive Menu...');
    await page.setViewportSize({ width: 375, height: 667 }); // iPhone size
    await page.goto('https://erp-filament.vercel.app/dashboard');
    await page.waitForLoadState('networkidle');
    
    // Check if hamburger menu is visible
    const hamburger = await page.locator('button').first();
    const hasHamburger = await hamburger.isVisible();
    console.log(`   ${hasHamburger ? '✅' : '❌'} Mobile menu button visible: ${hasHamburger}`);
    
    if (hasHamburger) {
      // Click hamburger
      await hamburger.click();
      await page.waitForTimeout(500);
      
      // Check if sidebar appears
      const mobileSidebar = await page.locator('.bg-gray-900');
      const isMobileSidebarVisible = await mobileSidebar.isVisible();
      console.log(`   ${isMobileSidebarVisible ? '✅' : '❌'} Mobile sidebar toggles: ${isMobileSidebarVisible}`);
    }
    
    console.log('\n✅ Testing Complete!');
    
  } catch (error) {
    console.error('❌ Test failed:', error);
  } finally {
    await browser.close();
  }
}

testERPApp();