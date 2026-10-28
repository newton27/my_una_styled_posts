const assert=require('node:assert/strict');
const fs=require('node:fs');const vm=require('node:vm');
let state='loading',callbacks={},observer;
const source={value:'Draft'};
const form={querySelector:()=>source};
const root={closest:()=>form,querySelector:()=>null};
const document={get readyState(){return state;},querySelectorAll:s=>s==='.nwsp-composer'?[root]:[],addEventListener:(n,cb)=>callbacks[n]=cb,documentElement:{}};
const context={document,window:{},MutationObserver:class{constructor(cb){observer=cb}observe(){}},requestAnimationFrame:cb=>cb()};
vm.runInNewContext(fs.readFileSync('modules/newton/my_una_styled_posts/js/composer.js','utf8'),context);
assert.doesNotThrow(()=>context.window.NwStyledPosts.scan(),'A partially streamed composer must not throw while parsing');
state='complete';
assert.doesNotThrow(()=>callbacks.DOMContentLoaded(),'A missing configuration must stay retryable, not throw');
// A later mutation inside an incomplete composer must trigger another scan.
let scans=0;document.querySelectorAll=s=>{if(s==='.nwsp-composer')scans++;return s==='.nwsp-composer'?[root]:[]};
const script={nodeType:1,matches:()=>false,querySelector:()=>null};
observer([{target:{nodeType:1,closest:()=>root},removedNodes:[],addedNodes:[script]}]);
assert.equal(scans,1,'Appending configuration inside an incomplete composer must retry mounting');
console.log('Composer startup: partial document, absent config and later retry PASS');
