import {spawn} from 'node:child_process';
import {mkdtemp,writeFile} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join,resolve} from 'node:path';
import {pathToFileURL} from 'node:url';

// Local synthetic fixtures only; never connects to a production website.
const root=resolve('storage/cache/theme-preview');
const profile=await mkdtemp(join(tmpdir(),'rahayat-theme-audit-'));
const edge=spawn('C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',[
  '--headless=new','--disable-gpu','--no-first-run','--no-default-browser-check',
  '--remote-debugging-port=0',`--user-data-dir=${profile}`,'about:blank',
],{windowsHide:true,stdio:['ignore','ignore','pipe']});
const endpoint=await new Promise((res,rej)=>{
  let buffer=''; const timeout=setTimeout(()=>rej(new Error('Edge startup timed out')),20000);
  edge.on('error',rej);
  edge.stderr.on('data',chunk=>{buffer+=chunk;const match=buffer.match(/DevTools listening on (ws:\/\/[^\s]+)/);if(match){clearTimeout(timeout);res(match[1]);}});
});
const socket=new WebSocket(endpoint); await new Promise((res,rej)=>{socket.onopen=res;socket.onerror=rej;});
let sequence=0;const pending=new Map();
socket.onmessage=({data})=>{const msg=JSON.parse(data);if(msg.id&&pending.has(msg.id)){const {res,rej}=pending.get(msg.id);pending.delete(msg.id);msg.error?rej(new Error(msg.error.message)):res(msg.result);}};
const command=(method,params={},sessionId)=>new Promise((res,rej)=>{const id=++sequence;pending.set(id,{res,rej});socket.send(JSON.stringify({id,method,params,...(sessionId?{sessionId}:{})}));});
const {targetId}=await command('Target.createTarget',{url:'about:blank'});
const {sessionId}=await command('Target.attachToTarget',{targetId,flatten:true});
const call=(method,params={})=>command(method,params,sessionId);
await call('Page.enable');await call('Runtime.enable');
await call('DOM.enable');await call('CSS.enable');
const themes=['nawala-indigo','arunika-emerald','wibawa-crimson','samudra-cendekia'];
const pages=['home','profile','news','article','gallery','gtk','contact','agenda','calendar','achievements','album','event','achievement','program','principal','alumni-detail','testimonials','alumni','spmb','register','status'];
const audit=String(function(){
  const canvas=document.createElement('canvas');canvas.width=canvas.height=1;
  const ctx=canvas.getContext('2d',{willReadFrequently:true});
  const rgb=s=>{ctx.clearRect(0,0,1,1);ctx.fillStyle=s;ctx.fillRect(0,0,1,1);const c=[...ctx.getImageData(0,0,1,1).data];return [c[0],c[1],c[2],c[3]/255];};
  const lum=c=>c.slice(0,3).map(v=>{v/=255;return v<=.04045?v/12.92:((v+.055)/1.055)**2.4;}).reduce((s,v,i)=>s+v*[.2126,.7152,.0722][i],0);
  const failures=[];const uncertain=[];let checked=0;
  for(const el of document.body.querySelectorAll('*')){
    if(![...el.childNodes].some(n=>n.nodeType===3&&n.textContent.trim()))continue;
    const box=el.getBoundingClientRect(),style=getComputedStyle(el);
    if(!box.width||!box.height||box.right<0||box.left>innerWidth||style.visibility==='hidden'||style.display==='none'||Number(style.opacity)===0)continue;
    if(el.closest('details:not([open])')&&!el.closest('summary'))continue;
    const fg=rgb(style.color);if(fg.length<3)continue;
    let bg=null,gradient=false;
    for(let ancestor=el;ancestor;ancestor=ancestor.parentElement){const st=getComputedStyle(ancestor);if(st.backgroundImage!=='none')gradient=true;const c=rgb(st.backgroundColor);if(c.length>=3&&(c[3]??1)===1){bg=c;break;}}
    if(gradient||!bg||(fg[3]??1)<1){uncertain.push({tag:el.tagName,text:el.textContent.trim().slice(0,65)});continue;}
    const a=lum(fg),b=lum(bg),ratio=(Math.max(a,b)+.05)/(Math.min(a,b)+.05);
    const large=parseFloat(style.fontSize)>=24||(parseFloat(style.fontSize)>=18.66&&parseInt(style.fontWeight)>=700);
    checked++;if(ratio<(large?3:4.5))failures.push({tag:el.tagName,text:el.textContent.trim().slice(0,65),color:style.color,background:bg,ratio:Number(ratio.toFixed(2))});
  }
  return {checked,failures,uncertain,overflow:document.documentElement.scrollWidth>innerWidth+1,width:innerWidth,scrollWidth:document.documentElement.scrollWidth};
});
const results=[];
try{
  for(const theme of themes)for(const page of pages)for(const width of [1440,390]){
    await call('Emulation.setDeviceMetricsOverride',{width,height:1000,deviceScaleFactor:1,mobile:false});
    await call('Page.navigate',{url:pathToFileURL(join(root,`${theme}-${page}.html`)).href});
    await call('Runtime.evaluate',{expression:'Promise.race([new Promise(r=>{if(document.readyState==="complete")r();else window.addEventListener("load",()=>r(),{once:true});}),new Promise(r=>setTimeout(r,2500))])',awaitPromise:true});
    await call('Runtime.evaluate',{expression:'document.fonts.ready',awaitPromise:true});
    const doc=await call('DOM.getDocument');
    const interactive=await call('DOM.querySelectorAll',{nodeId:doc.root.nodeId,selector:'a,button,summary,input,select,textarea'});
    for(const state of ['normal','menu-open','hover','focus']){
      if(state==='menu-open')await call('Runtime.evaluate',{expression:'document.querySelectorAll("details").forEach(e=>e.open=true)'});
      if(state==='hover'||state==='focus')for(const nodeId of interactive.nodeIds)await call('CSS.forcePseudoState',{nodeId,forcedPseudoClasses:state==='hover'?['hover']:['focus','focus-visible']});
      const evaluated=await call('Runtime.evaluate',{expression:`(${audit})()`,returnByValue:true});
      results.push({theme,page,width,state,...evaluated.result.value});
    }
    if(page==='home'){
      await call('Runtime.evaluate',{expression:'document.querySelectorAll(".next-header details").forEach(e=>e.open=false)'});
      const shot=await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});
      await writeFile(join(root,`${theme}-${width}.png`),Buffer.from(shot.data,'base64'));
    }
  }
  await writeFile(join(root,'audit.json'),JSON.stringify(results,null,2));
  const failed=results.filter(r=>r.overflow||r.failures.length);
  console.log(JSON.stringify({states:results.length,checked:results.reduce((s,r)=>s+r.checked,0),failed:failed.length,uncertain:results.reduce((s,r)=>s+r.uncertain.length,0),failures:failed.slice(0,6).map(r=>({theme:r.theme,page:r.page,width:r.width,state:r.state,overflow:r.overflow,failures:r.failures}))},null,2));
  if(failed.length)process.exitCode=1;
}finally{socket.close();edge.kill();}
