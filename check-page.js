const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  
  const pages = [
    '/dashboard',
    '/stock-management',
    '/sales-history',
    '/sales-report',
    '/stock-report',
    '/pos',
    '/products',
    '/categories'
  ];
  
  console.log('Checking pages on Vercel deployment:\n');
  
  for (const path of pages) {
    try {
      await page.goto(`https://erp-filament.vercel.app${path}`);
      await page.waitForLoadState('networkidle', { timeout: 10000 });
      
      const title = await page.title();
      const h1Text = await page.$eval('h1', el => el.textContent).catch(() => 'No H1');
      const hasContent = await page.$('main, .min-h-screen').then(el => !!el);
      
      console.log(`✅ ${path}:`);
      console.log(`   Title: ${title}`);
      console.log(`   H1: ${h1Text}`);
      console.log(`   Has content: ${hasContent}`);
    } catch (error) {
      console.log(`❌ ${path}: ${error.message}`);
    }
  }
  
  await browser.close();
})();