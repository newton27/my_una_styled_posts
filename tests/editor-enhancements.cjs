const assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES]}));
(async()=>{
 const browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_EXECUTABLE_PATH,args:['--no-sandbox','--disable-gpu','--no-zygote','--single-process']});
 const page=await browser.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto('http://127.0.0.1:8940/preview/index.html');
 await page.locator('.nwsp-readability summary').click();
 await page.locator('[data-control=text_backdrop]').selectOption('box');
 await page.locator('[data-control=backdrop_color]').fill('#123456');
 async function slide(key,value){await page.locator('[data-control='+key+']').evaluate((e,v)=>{e.value=v;e.dispatchEvent(new Event('input',{bubbles:true}));},String(value));}
 await slide('backdrop_opacity',75);await slide('backdrop_padding',20);
 assert.equal(await page.locator('[name=backdrop_opacity]').inputValue(),'75');
 assert.equal(await page.locator('.nwsp-text-content').evaluate(e=>getComputedStyle(e).backgroundColor),'rgba(18, 52, 86, 0.75)');
 assert.equal(await page.locator('.nwsp-text-content').evaluate(e=>getComputedStyle(e).opacity),'1');
 await page.locator('[data-background=mountains]').click();
 await page.locator('.nwsp-image-adjust summary').click();await slide('image_x',0);await slide('image_y',100);await slide('image_zoom',200);
 assert.equal(await page.locator('[name=image_zoom]').inputValue(),'200');
 assert.equal(await page.locator('img.nwsp-background').evaluate(e=>getComputedStyle(e).objectPosition),'0% 100%');
 assert.match(await page.locator('img.nwsp-background').evaluate(e=>getComputedStyle(e).transform),/^matrix\(2,/);
 await page.locator('[data-reset-image]').click();assert.equal(await page.locator('[name=image_zoom]').inputValue(),'100');
 await page.locator('[data-control=font_name]').selectOption('amiri');await page.locator('[data-control=text_direction]').selectOption('rtl');
 await page.locator('[name=text]').fill('الحياة جميلة — Hello 123\nاللَّهُمَّ اجعل أيامنا سلامًا');await page.evaluate(()=>document.fonts.ready);
 assert.equal(await page.locator('.nwsp-text').getAttribute('dir'),'rtl');
 assert.equal(await page.evaluate(()=>document.fonts.check('48px "NW Amiri"')),true);
 for(const width of [360,1280]){await page.setViewportSize({width,height:1000});await slide('backdrop_padding',32);await page.locator('[name=text]').fill('اللَّهُمَّ سلام 123 '.repeat(25));await page.waitForTimeout(120);assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true);assert.equal(await page.locator('.nwsp-card').evaluate(el=>{const t=el.querySelector('.nwsp-text'),c=getComputedStyle(el);return t.scrollHeight<=el.clientHeight-parseFloat(c.paddingTop)-parseFloat(c.paddingBottom)+2;}),true,'All Arabic text fits');}
 await page.setViewportSize({width:360,height:1000});await slide('image_zoom',250);await page.locator('[name=text]').fill(('Line\n').repeat(95));await page.waitForTimeout(120);assert.equal(await page.locator('.nwsp-card').evaluate(e=>getComputedStyle(e).overflow),'hidden','Long zoomed cards retain media clipping');assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true,'Zoomed media must not widen page');
 await page.locator('[data-control=text_backdrop]').selectOption('none');assert.equal(await page.locator('.nwsp-text-content').evaluate(e=>getComputedStyle(e).padding),'0px');
 assert.equal(await page.locator('a[href="https://www.pexels.com/"]').getAttribute('target'),'_blank');assert.deepEqual(errors,[]);
 await page.locator('[name=text]').fill('الحياة جميلة\nA little kindness goes a long way.');await page.locator('[data-control=text_backdrop]').selectOption('box');await page.locator('.nwsp-readability').evaluate(e=>e.open=true);await page.screenshot({path:'preview/editor-enhancements.png',fullPage:true});
 await browser.close();console.log('Editor browser checks passed: backdrop opacity/padding, framing/reset, Arabic font/RTL, long mobile text and image links.');
})().catch(e=>{console.error(e);process.exit(1)});
