const { chromium } = require('playwright');

async function testAllMenus() {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  console.log('🔍 Testing ALL Menu Items on ERP Application...\n');
  
  try {
    // Go to dashboard
    await page.goto('https://erp-filament.vercel.app/dashboard');
    await page.waitForLoadState('networkidle');
    
    // Get all menu items from the sidebar
    const menuItems = await page.$$eval('.bg-gray-900 a', links => 
      links.map(link => ({
        text: link.textContent.trim(),
        href: link.getAttribute('href'),
        classes: link.className
      }))
    );
    
    console.log('Found menu items:');
    console.log('================\n');
    
    const results = [];
    
    for (const item of menuItems) {
      console.log(`Testing: ${item.text} (${item.href})`);
      
      try {
        // Click the menu item
        const link = await page.$(`a[href="${item.href}"]`);
        if (link) {
          await link.click();
          await page.waitForLoadState('networkidle', { timeout: 5000 });
          
          const currentUrl = page.url();
          const pageTitle = await page.title();
          const hasContent = await page.$('h1, h2, main').then(el => !!el);
          
          if (currentUrl.includes(item.href)) {
            console.log(`  ✅ Works - Navigated to ${item.href}`);
            results.push({ menu: item.text, href: item.href, status: 'Works' });
          } else {
            console.log(`  ❌ Failed - Still on ${currentUrl}`);
            results.push({ menu: item.text, href: item.href, status: 'Failed' });
          }
        } else {
          console.log(`  ⚠️  Link not found`);
          results.push({ menu: item.text, href: item.href, status: 'Not Found' });
        }
        
        // Go back to dashboard for next test
        await page.goto('https://erp-filament.vercel.app/dashboard');
        await page.waitForLoadState('networkidle');
        
      } catch (error) {
        console.log(`  ❌ Error: ${error.message}`);
        results.push({ menu: item.text, href: item.href, status: 'Error' });
        
        // Try to recover
        await page.goto('https://erp-filament.vercel.app/dashboard').catch(() => {});
      }
    }
    
    // Summary
    console.log('\n\nSUMMARY');
    console.log('=======\n');
    
    const working = results.filter(r => r.status === 'Works');
    const failed = results.filter(r => r.status !== 'Works');
    
    console.log(`✅ Working: ${working.length} menus`);
    working.forEach(r => console.log(`   - ${r.menu} (${r.href})`));
    
    console.log(`\n❌ Not Working: ${failed.length} menus`);
    failed.forEach(r => console.log(`   - ${r.menu} (${r.href}) - ${r.status}`));
    
  } catch (error) {
    console.error('Test failed:', error);
  } finally {
    await browser.close();
  }
}

testAllMenus();