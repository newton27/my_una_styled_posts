const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm');
let fontDone;
const context={document:{readyState:'loading',fonts:{addEventListener:(name,cb)=>{if(name==='loadingdone')fontDone=cb;}},addEventListener(){},documentElement:{}},window:{},MutationObserver:class{observe(){}},getComputedStyle:()=>({paddingLeft:'24',paddingRight:'24',paddingTop:'24',paddingBottom:'24'})};
vm.runInNewContext(fs.readFileSync('modules/newton/my_una_styled_posts/js/composer.js','utf8'),context);
assert.equal(typeof context.window.NwStyledPosts.positionCard,'function','Shared responsive position layout must exist');
function fixture(w,h,tw,th,x,y,mode='custom'){const props={};const text={offsetWidth:tw,offsetHeight:th,style:{setProperty:(k,v)=>props[k]=v}};return {card:{clientWidth:w,clientHeight:h,dataset:{textPosition:mode,textX:x,textY:y},querySelector:()=>text},props};}
for(const [x,y,dx,dy] of [[0,0,-126,-106],[50,50,0,0],[100,100,126,106]]){const f=fixture(400,300,100,40,x,y);context.window.NwStyledPosts.positionCard(f.card);assert.equal(f.props['--nwsp-text-dx'],dx+'px');assert.equal(f.props['--nwsp-text-dy'],dy+'px');}
const full=fixture(200,200,200,220,100,100);context.window.NwStyledPosts.positionCard(full.card);assert.equal(full.props['--nwsp-text-dx'],'0px');assert.equal(full.props['--nwsp-text-dy'],'0px');
console.log('Position geometry: center, corners and no travel room PASS');

assert.equal(typeof fontDone,'function','Fonts loaded after startup must trigger layout');
