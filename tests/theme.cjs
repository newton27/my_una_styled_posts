const assert=require('node:assert/strict');
const path=require('node:path');
const {pathToFileURL}=require('node:url');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES]}));
(async()=>{
 const browser=await chromium.launch({headless:true,args:['--no-sandbox','--disable-gpu','--no-zygote','--single-process'],...(process.env.CHROME_EXECUTABLE_PATH?{executablePath:process.env.CHROME_EXECUTABLE_PATH}:{})});
 const page=await browser.newPage({viewport:{width:1100,height:1000}});
 await page.goto(pathToFileURL(path.resolve('preview/index.html')).href);
 const selectors=['.nwsp-toolbar label','.nwsp-toolbar output','.nwsp-background-heading strong','.nwsp-background-heading span','.nwsp-preview-footer','.nwsp-help','.nwsp-feedback'];
 for(const [theme,bg,fg] of [['light','#ffffff','#292638'],['dark','#202b3a','#d5dbea']]){
  await page.addStyleTag({content:`.editor{background:${bg};color:${fg}}`});
  const results=await page.evaluate(({selectors,bg})=>{
   const hex=rgb=>'#'+rgb.match(/[\d.]+/g).slice(0,3).map(v=>Math.round(Number(v)).toString(16).padStart(2,'0')).join('');
   return selectors.map(selector=>({selector,color:getComputedStyle(document.querySelector(selector)).color,ratio:NwStyledPosts.contrast(hex(getComputedStyle(document.querySelector(selector)).color),bg)}));
  },{selectors,bg});
  for(const result of results)assert.ok(result.ratio>=4.5,`${theme} ${result.selector}: contrast ${result.ratio.toFixed(2)} (${result.color})`);
 }
 await page.screenshot({path:'preview/dark-theme.png',fullPage:true});
 await browser.close();console.log('14 light/dark label contrast checks passed.');
})().catch(e=>{console.error(e);process.exit(1)});
