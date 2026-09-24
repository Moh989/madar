import {chromium,expect} from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import fs from 'node:fs';
const browser=await chromium.launch({headless:true});
const report={viewports:[],accessibility:[],checks:[],errors:[]};
const context=await browser.newContext({viewport:{width:1440,height:1000},reducedMotion:'reduce'});
const page=await context.newPage();
page.on('pageerror',e=>report.errors.push(e.message));
const base='http://127.0.0.1:8000';
try{
for(const width of [360,390,768,1440,1920]){
 await page.setViewportSize({width,height:900});
 for(const path of ['/ar','/en','/ar/sectors','/ar/sectors/air-freight','/ar/contact','/ar/quote','/en/about']){
  const response=await page.goto(base+path);expect(response.status()).toBe(200);await page.evaluate(()=>document.fonts.ready);
  const overflow=await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth);expect(overflow,`${path} width ${width}`).toBe(false);
 }
 report.viewports.push({width,pages:7,overflow:false});
}
await page.setViewportSize({width:1440,height:1000});await page.goto(base+'/ar');
expect(await page.locator('[data-slide]').count()).toBe(4);await expect(page.locator('[data-current]')).toHaveText('01');
for(const index of [1,2,3,0]){await page.locator(`[data-dot="${index}"]`).click();await expect(page.locator('[data-current]')).toHaveText(String(index+1).padStart(2,'0'));expect(await page.locator('[data-slide].is-active img').evaluate(img=>img.complete&&img.naturalWidth>0)).toBe(true);}
await page.locator('[data-slider]').focus();await page.keyboard.press('ArrowLeft');await expect(page.locator('[data-current]')).toHaveText('02');report.checks.push('Four slides, image decoding, dots and keyboard arrows');
await page.evaluate(()=>scrollTo(0,1000));await expect(page.locator('[data-header]')).toHaveClass(/scrolled/);expect(await page.locator('[data-header]').evaluate(e=>e.getBoundingClientRect().top)).toBe(0);report.checks.push('Fixed navigation and scroll color change');
await page.goto(base+'/ar/sectors/air-freight');await page.locator('.language').click();await expect(page).toHaveURL(/\/en\/sectors\/air-freight$/);report.checks.push('Language switch retains detail route');
for(const path of ['/ar','/en','/ar/contact','/ar/sectors/air-freight','/ar/sectors','/ar/about','/ar/quote']){
 await page.goto(base+path);const result=await new AxeBuilder({page}).withTags(['wcag2a','wcag2aa','wcag21aa','wcag22aa']).analyze();report.accessibility.push({path,violations:result.violations.map(v=>({id:v.id,impact:v.impact,description:v.description,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))}))});
}
await page.setViewportSize({width:390,height:844});await page.goto(base+'/ar');await page.locator('[data-menu-open]').click();await expect(page.locator('#mobile-menu')).toBeVisible();await page.keyboard.press('Tab');expect(await page.evaluate(()=>document.querySelector('#mobile-menu').contains(document.activeElement))).toBe(true);await page.keyboard.press('Escape');await expect(page.locator('#mobile-menu')).not.toBeVisible();await expect(page.locator('[data-menu-open]')).toBeFocused();report.checks.push('Mobile dialog, keyboard focus and Escape return');
await page.goto(base+'/ar/quote?sector=1');await expect(page.locator('[data-shipping-fields]')).toBeVisible();await page.locator('#sector_id').selectOption('3');await expect(page.locator('[data-shipping-fields]')).toBeHidden();await page.locator('#sector_id').selectOption('1');
const csrf=await context.request.post(base+'/ar/inquiries',{headers:{'Sec-Fetch-Site':'cross-site','Accept':'application/json'},form:{type:'contact',name:'QA'}});expect(csrf.status()).toBe(419);report.checks.push('Real HTTP CSRF rejection');
await page.locator('#name').fill('QA Browser Request');await page.locator('#contact').fill('browser-qa@example.test');await page.locator('#details').fill('QA browser test: shipment request saved in MySQL.');await page.locator('#origin').fill('Baghdad');await page.locator('#destination').fill('Test destination');await page.locator('.submit-button').click();await expect(page.locator('.alert.success')).toBeVisible();report.checks.push('Shipment form conditional fields and saved request confirmation');
const credentials=JSON.parse(fs.readFileSync('/tmp/madar-qa-credentials.json','utf8'));
await page.goto(base+'/admin/login');await page.locator('#email').fill(credentials.email);await page.locator('#password').fill(credentials.password);await page.locator('.login-form button').click();await expect(page).toHaveURL(base+'/admin');
await page.goto(base+'/admin/inquiries');await expect(page.getByRole('heading',{name:'QA Browser Request'})).toBeVisible();const card=page.locator('.inquiry-record').filter({has:page.getByRole('heading',{name:'QA Browser Request'})});await card.locator('select').selectOption('following');await card.locator('textarea').fill('QA checked in browser');await card.getByRole('button',{name:'حفظ المتابعة'}).click();await expect(page.locator('.alert.success')).toBeVisible();report.checks.push('Admin login and MySQL inquiry follow-up');
await page.goto(base+'/admin/media');await page.locator('#file').setInputFiles('public/images/air-small.webp');await page.locator('#alt_ar').fill('صورة اختبار الإدارة');await page.locator('#alt_en').fill('QA browser image');await page.locator('#source').fill('QA generated image');await page.locator('#license').fill('Generated for this project');await page.getByRole('button',{name:'رفع الصورة'}).click();await expect(page.locator('.alert.success')).toBeVisible();report.checks.push('Image upload through admin browser UI');
await page.goto(base+'/admin/content/news/create');await page.locator('#slug').fill('qa-browser-news');await page.locator('#title_ar').fill('خبر اختبار الإدارة');await page.locator('#title_en').fill('QA browser news');await page.locator('#body_ar').fill('محتوى اختبار ظهور التحديثات في الموقع.');await page.locator('#body_en').fill('Browser test content for publication verification.');await page.locator('#status').selectOption('published');await page.getByRole('button',{name:'حفظ التغييرات'}).first().click();await expect(page.locator('.alert.success')).toBeVisible();await page.goto(base+'/ar/news/qa-browser-news');await expect(page.getByRole('heading',{name:'خبر اختبار الإدارة',exact:true})).toBeVisible();report.checks.push('Admin content publication reflected in public page');
await page.goto(base+'/admin');await page.getByRole('button',{name:'تسجيل الخروج'}).click();await page.goto(base+'/admin/inquiries');await expect(page).toHaveURL(base+'/admin/login');report.checks.push('Logout invalidates protected access');
const motion=await browser.newPage({viewport:{width:1440,height:1000}});await motion.goto(base+'/ar');await expect(motion.locator('[data-current]')).toHaveText('01');await expect(motion.locator('[data-current]')).toHaveText('02',{timeout:8000});await motion.locator('[data-slider]').focus();await motion.waitForTimeout(6300);await expect(motion.locator('[data-current]')).toHaveText('02');await motion.locator('[data-pause-toggle]').click();await motion.locator('.brand').first().focus();await motion.waitForTimeout(6300);await expect(motion.locator('[data-current]')).toHaveText('02');await motion.close();report.checks.push('6-second autoplay, pause on focus, persistent pause');
const nojs=await browser.newPage({javaScriptEnabled:false,viewport:{width:390,height:844}});await nojs.goto(base+'/ar');await expect(nojs.locator('.hero-slide.is-active h1')).toBeVisible();await expect(nojs.locator('.nojs-mobile')).toBeVisible();expect(await nojs.locator('.hero-slide.is-active img').evaluate(i=>i.complete&&i.naturalWidth>0)).toBe(true);await nojs.close();report.checks.push('First slide, image and navigation available without JavaScript');
report.passed=true;
}catch(e){report.passed=false;report.failure=e.stack;console.error(e.message);}
fs.writeFileSync('docs/browser-report.json',JSON.stringify(report,null,2));
console.log(JSON.stringify({passed:report.passed,checks:report.checks.length,viewports:report.viewports.length,violations:report.accessibility.map(x=>({path:x.path,count:x.violations.length})),errors:report.errors},null,2));await browser.close();if(!report.passed)process.exitCode=1;
