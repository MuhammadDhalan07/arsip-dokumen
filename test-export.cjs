const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({
        headless: false,
        executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        args: [
            '--host-resolver-rules=MAP arsip-dokumen.test 127.0.0.1',
            '--ignore-certificate-errors'
        ]
    });
    const context = await browser.newContext();
    const page = await context.newPage();

    console.log('1. Navigating to https://arsip-dokumen.test/admin...');
    
    page.on('console', msg => console.log('Browser:', msg.text()));
    page.on('pageerror', error => console.log('Page Error:', error.message));
    
    try {
        await page.goto('https://arsip-dokumen.test/admin', { 
            waitUntil: 'networkidle',
            timeout: 30000
        });
        
        console.log('2. Page loaded. Checking page content...');
        
        const title = await page.title();
        console.log('Page title:', title);
        
        const bodyText = await page.locator('body').textContent();
        console.log('Body preview:', bodyText.substring(0, 500));
        
        if (bodyText.includes('Masuk') || bodyText.includes('Login') || bodyText.includes('Email')) {
            console.log('3. Login page detected. Please login manually.');
        }
    } catch (error) {
        console.log('Navigation error:', error.message);
    }
    
    console.log('4. Test completed!');
    await browser.close();
})();
