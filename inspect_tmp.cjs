const puppeteer = require('puppeteer-core');

(async () => {
  const browser = await puppeteer.launch({
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
    headless: 'new',
    args: ['--no-sandbox'],
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1920, height: 1000 });
  await page.goto('http://127.0.0.1:8000/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await new Promise((r) => setTimeout(r, 3000));

  const targetY = await page.evaluate(() => {
    const el = document.querySelector('.traveller-stories-section');
    const rect = el.getBoundingClientRect();
    return rect.top + window.scrollY;
  });

  await page.evaluate((y) => window.scrollTo(0, y), targetY);

  await page.waitForFunction(() => {
    const img = document.querySelector('.stories-decor--bg');
    return img && img.complete && img.naturalWidth > 0;
  }, { timeout: 20000 });

  await new Promise((r) => setTimeout(r, 1000));

  await page.screenshot({ path: 'C:/Users/Dell/AppData/Local/Temp/claude/c--xampp-htdocs-Aangi-travels/559d0fed-5473-42b6-b070-c16e9b3727fa/scratchpad/testimonials_bgimg2.png' });

  await browser.close();
})();
