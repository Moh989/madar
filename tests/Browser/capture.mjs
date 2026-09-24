import {chromium} from '@playwright/test';
const browser=await chromium.launch({headless:true});
for(const [name,width,height] of [['desktop',1440,1000],['mobile',390,844]]){
 const page=await browser.newPage({viewport:{width,height},deviceScaleFactor:1,reducedMotion:'reduce'});
 page.on('pageerror',err=>console.log('ERROR',err.message));
 await page.goto('http://127.0.0.1:8000/ar');await page.evaluate(()=>document.fonts.ready);
 await page.screenshot({path:`docs/screenshots/home-${name}.png`,fullPage:true});
 await page.screenshot({path:`docs/screenshots/home-${name}-viewport.png`});
 console.log(name,await page.title(),await page.evaluate(()=>({scroll:document.documentElement.scrollWidth,width:innerWidth,images:[...document.images].filter(i=>i.src&&!i.complete).length})));
 await page.close();
}
await browser.close();
