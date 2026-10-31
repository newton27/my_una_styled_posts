const assert=require('node:assert/strict');
const {pathToFileURL}=require('node:url');
const path=require('node:path');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||path.resolve('node_modules')]}));
(async()=>{
 const browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_EXECUTABLE_PATH,args:['--no-sandbox']});
 const context=await browser.newContext({viewport:{width:1280,height:1000},hasTouch:true});
 const page=await context.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto(pathToFileURL(path.resolve('preview/index.html')).href);await page.evaluate(()=>document.fonts.ready);
 const card=page.locator('.nwsp-composer .nwsp-card'),text=card.locator('.nwsp-text');
 await page.locator('[name=text]').fill('Move me');
 assert.equal(await card.getAttribute('data-text-position'),'auto','Existing post keeps original layout');
 await text.scrollIntoViewIfNeeded();
 async function bounds(){return card.evaluate(el=>{const t=el.querySelector('.nwsp-text').getBoundingClientRect(),c=el.getBoundingClientRect(),s=getComputedStyle(el);return {x:t.x,y:t.y,width:t.width,height:t.height,left:c.left+parseFloat(s.paddingLeft),right:c.right-parseFloat(s.paddingRight),top:c.top+parseFloat(s.paddingTop),bottom:c.bottom-parseFloat(s.paddingBottom)};});}
 async function contained(){const b=await bounds();assert.ok(b.x>=b.left-2&&b.x+b.width<=b.right+2&&b.y>=b.top-2&&b.y+b.height<=b.bottom+2,JSON.stringify(b));}
 let b=await text.boundingBox();await page.mouse.move(b.x+b.width/2,b.y+b.height/2);await page.mouse.down();await page.mouse.move(b.x+b.width/2+800,b.y+b.height/2+600,{steps:8});await page.mouse.up();
 assert.equal(await page.locator('[name=text_x]').inputValue(),'100');assert.equal(await page.locator('[name=text_y]').inputValue(),'100');await contained();
 // Saved data renders through the same public card layout after reopening.
 const state=await page.locator('.nwsp-config').evaluate(el=>JSON.parse(el.textContent));
 const saved=await page.locator('form').evaluate(f=>Object.fromEntries(new FormData(f)));
 assert.equal(saved.text_position,'custom');
 const publicCard=await card.evaluate(el=>{const clone=el.cloneNode(true);clone.id='saved-card';clone.querySelector('.nwsp-text').removeAttribute('tabindex');document.body.append(clone);window.NwStyledPosts.scan();return clone.dataset.textX;});assert.equal(publicCard,'100');
 assert.equal(await page.locator('#saved-card .nwsp-text').getAttribute('tabindex'),null,'Published card is not draggable');
 for(const width of [360,1280]){await page.setViewportSize({width,height:1000});await page.waitForTimeout(100);await contained();}
 await text.focus();await page.keyboard.press('ArrowLeft');assert.equal(await page.locator('[name=text_x]').inputValue(),'99');await page.keyboard.press('Shift+ArrowUp');assert.equal(await page.locator('[name=text_y]').inputValue(),'90');
 b=await text.boundingBox();await page.mouse.move(b.x+3,b.y+3);await page.mouse.down();await page.mouse.move(b.x-100,b.y-100);await text.dispatchEvent('pointercancel',{pointerId:1,isPrimary:true});await page.mouse.up();assert.equal(await page.locator('[name=text_x]').inputValue(),'99');assert.equal(await text.evaluate(el=>el.classList.contains('nwsp-dragging')),false);
 await page.locator('[data-reset-position]').click();assert.equal(await card.getAttribute('data-text-position'),'auto');assert.equal(await page.locator('[name=text_x]').inputValue(),'50');
 await page.locator('[data-align=left]').click();const original=await bounds();await page.locator('[data-reset-position]').click();assert.deepEqual(await bounds(),original,'Reset preserves original left alignment');
 // Real touch events on the movable text preserve page scrolling outside the box.
 await text.scrollIntoViewIfNeeded();b=await text.boundingBox();const cdp=await context.newCDPSession(page);
 await cdp.send('Input.dispatchTouchEvent',{type:'touchStart',touchPoints:[{x:b.x+b.width/2,y:b.y+b.height/2}]});
 await cdp.send('Input.dispatchTouchEvent',{type:'touchMove',touchPoints:[{x:b.x+b.width/2+30,y:b.y+b.height/2-40}]});
 await cdp.send('Input.dispatchTouchEvent',{type:'touchEnd',touchPoints:[]});assert.equal(await card.getAttribute('data-text-position'),'custom');await contained();
 await page.locator('.nwsp-readability summary').click();await page.locator('[data-control=text_backdrop]').selectOption('box');
 // Delay an unused font until positioning has already used fallback metrics.
 let fontRoute, fontRequested;const requested=new Promise(resolve=>fontRequested=resolve);
 await page.route('**/Amiri-Regular.ttf',route=>{fontRoute=route;fontRequested();});
 await text.focus();for(let i=0;i<10;i++)await page.keyboard.press('Shift+ArrowRight');
 await page.locator('[data-control=font_name]').selectOption('amiri');await requested;
 await fontRoute.fulfill({path:path.resolve('modules/newton/my_una_styled_posts/template/fonts/Amiri-Regular.ttf'),contentType:'font/ttf'});
 await page.evaluate(()=>document.fonts.ready);await page.waitForTimeout(100);await contained();
 await page.locator('[data-control=text_direction]').selectOption('rtl');
 for(const width of [360,1280]){await page.setViewportSize({width,height:1000});await page.locator('[name=text]').fill('اللَّهُمَّ سلام 123 '.repeat(25));await page.waitForTimeout(100);await contained();}
 // New text, font loading, backdrop padding and long overflow keep bounds valid.
 await page.locator('[data-control=backdrop_padding]').evaluate(e=>{e.value='32';e.dispatchEvent(new Event('input',{bubbles:true}));});await contained();
 await page.locator('[name=text]').fill(('Line\n').repeat(95));await page.waitForTimeout(100);await contained();
 assert.deepEqual(errors,[]);
 await page.locator('[name=text]').fill('Your words.\nYour place.');await page.locator('[data-control=font_name]').selectOption('modern');await page.locator('[data-control=text_direction]').selectOption('auto');await text.focus();await page.keyboard.press('Shift+ArrowUp');await page.screenshot({path:'preview/text-position.png',fullPage:true});
 await browser.close();console.log('Text positioning browser checks PASS: mouse, touch, bounds, reset, keyboard, cancellation, saved card, mobile, Arabic and long text.');
})().catch(e=>{console.error(e);process.exit(1)});
