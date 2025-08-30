const { chromium } = require('playwright');

async function testNavigation() {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();
  
  console.log('🔍 Testing Navigation Links...\n');
  
  try {
    // Go to dashboard
    await page.goto('https://erp-filament.vercel.app/dashboard');
    await page.waitForLoadState('networkidle');
    
    // Wait for sidebar
    await page.waitForSelector('.bg-gray-900', { timeout: 5000 });
    
    // Get all links in the sidebar
    const links = await page.$$eval('.bg-gray-900 a', elements => 
      elements.map(el => ({
        text: el.textContent.trim(),
        href: el.getAttribute('href')
      }))
    );
    
    console.log('Found links in sidebar:');
    links.forEach(link => {
      console.log(`  - ${link.text}: ${link.href}`);
    });
    
    // Test clicking POS Terminal
    console.log('\nTesting POS Terminal click...');
    const posLink = await page.$('a:has-text("POS Terminal")');
    if (posLink) {
      await posLink.click();
      await page.waitForLoadState('networkidle');
      console.log('Current URL:', page.url());
    } else {
      console.log('POS Terminal link not found');
    }
    
  } catch (error) {
    console.error('Error:', error.message);
  } finally {
    await browser.close();
  }
}

testNavigation();