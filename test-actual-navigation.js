const { chromium } = require('playwright');

async function testActualNavigation() {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  console.log('🔍 Testing ACTUAL Navigation on Live Site...\n');
  
  const menuTests = [
    { name: 'Dashboard', expectedUrl: '/dashboard' },
    { name: 'POS Terminal', expectedUrl: '/pos' },
    { name: 'Sales History', expectedUrl: '/sales-history' },
    { name: 'Stock Management', expectedUrl: '/stock-management' },
    { name: 'Products', expectedUrl: '/products' },
    { name: 'Categories', expectedUrl: '/categories' },
    { name: 'Incoming Goods', expectedUrl: '/incoming-goods' },
    { name: 'Outgoing Goods', expectedUrl: '/outgoing-goods' },
    { name: 'Income', expectedUrl: '/income' },
    { name: 'Expenses', expectedUrl: '/expenses' },
    { name: 'Sales Report', expectedUrl: '/sales-report' },
    { name: 'Stock Report', expectedUrl: '/stock-report' }
  ];
  
  try {
    // Go to dashboard
    await page.goto('https://erp-filament.vercel.app/dashboard');
    await page.waitForLoadState('networkidle');
    console.log('✅ Dashboard loaded\n');
    
    for (const menu of menuTests) {
      console.log(`Testing: ${menu.name}`);
      
      try {
        // Try to click the menu item
        await page.click(`text="${menu.name}"`, { timeout: 3000 });
        
        // Wait for navigation or timeout
        await page.waitForURL(`**${menu.expectedUrl}`, { timeout: 5000 }).catch(() => {});
        
        const currentUrl = page.url();
        
        if (currentUrl.includes(menu.expectedUrl)) {
          console.log(`  ✅ SUCCESS - Navigated to ${menu.expectedUrl}`);
          
          // Check if page has content
          const hasH1 = await page.$('h1').then(el => !!el);
          if (hasH1) {
            const h1Text = await page.$eval('h1', el => el.textContent);
            console.log(`     Page title: ${h1Text}`);
          }
        } else {
          console.log(`  ❌ FAILED - Still on ${currentUrl.split('.app')[1]}`);
        }
        
        // Go back to dashboard
        if (!currentUrl.includes('/dashboard')) {
          await page.goto('https://erp-filament.vercel.app/dashboard');
          await page.waitForLoadState('networkidle');
        }
        
      } catch (error) {
        console.log(`  ❌ ERROR: ${error.message.split('\n')[0]}`);
      }
      
      console.log('');
    }
    
  } catch (error) {
    console.error('Test failed:', error);
  } finally {
    setTimeout(() => browser.close(), 2000);
  }
}

testActualNavigation();