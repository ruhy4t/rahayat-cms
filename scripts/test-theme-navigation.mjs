import assert from 'node:assert/strict';
import {readFile} from 'node:fs/promises';
import vm from 'node:vm';

const listeners = new Map();
const branch = {open:false};
const link = {};
const submenuLink = {};
const parent = {
    querySelector: () => branch,
    addEventListener: (name, callback) => listeners.set(name, callback),
    contains: node => node === link || node === submenuLink,
};
let desktop = true;
const document = {
    activeElement: null,
    addEventListener: (name, callback) => { if (name === 'DOMContentLoaded') callback(); },
    querySelector: selector => selector === '.next-header' ? header : null,
};
const header = {
    querySelectorAll: selector => selector === '.next-desktop-nav .next-menu-parent' ? [parent] : [],
};
const context = vm.createContext({document, window:{matchMedia: () => ({get matches(){return desktop;}})}});
vm.runInContext(await readFile(new URL('../public/js/next-themes.js', import.meta.url), 'utf8'), context);
listeners.get('pointerenter')({pointerType:'mouse'});
assert.equal(branch.open,true,'Mouse opens submenu without clicking');
listeners.get('pointerleave')();
assert.equal(branch.open,false,'Leaving closes submenu');
listeners.get('focusin')();
assert.equal(branch.open,true,'Keyboard focus opens submenu');
listeners.get('focusout')({relatedTarget:submenuLink});
assert.equal(branch.open,true,'Moving focus into submenu keeps it open');
listeners.get('focusout')({relatedTarget:null});
assert.equal(branch.open,false,'Moving focus away closes submenu');
desktop=false;
listeners.get('pointerenter')({pointerType:'touch'});
assert.equal(branch.open,false,'Mobile touch does not trigger desktop hover');
desktop=true;
listeners.get('pointerenter')({pointerType:'touch'});
assert.equal(branch.open,false,'Touch on hybrid desktop keeps native tap behavior');
console.log('PASS: desktop hover, keyboard focus, submenu traversal, mobile and hybrid touch navigation.');

// A gallery reached through SPA navigation has no viewer at DOMContentLoaded.
const pageEvents = new Map();
let currentViewer = null;
let focused = null;
let modalCalls = 0;
const controls = Object.fromEntries(['img','[data-gallery-caption]','[data-gallery-previous]','[data-gallery-next]','[data-gallery-close]'].map(key => [key, {
    events: new Map(), addEventListener(name, fn) {this.events.set(name, fn);},
    focus(options) {assert.equal(options.preventScroll, true); focused = this;},
}]));
const pictures = [0,1].map(index => ({
    dataset:{gallerySrc:`/storage/photo-${index}.jpg`,caption:`Foto ${index}`}, isConnected:true,
    events:new Map(), addEventListener(name, fn){assert.equal(this.events.has(name),false,'Photo initialized more than once');this.events.set(name,fn);},
    focus(options){assert.equal(options.preventScroll,true);focused=this;},
}));
const galleryDocument = {
    addEventListener(name, fn){pageEvents.set(name,fn);},
    querySelector(selector){return selector==='.next-header'?{querySelectorAll:()=>[]}:selector==='[data-gallery-viewer]'?currentViewer:null;},
    querySelectorAll(){return pictures;},
};
vm.runInNewContext(await readFile(new URL('../public/js/next-themes.js', import.meta.url),'utf8'),{document:galleryDocument,window:{matchMedia:()=>({matches:true})}});
pageEvents.get('DOMContentLoaded')();
currentViewer = {
    dataset:{},events:new Map(),open:false,
    querySelector(selector){return controls[selector];},
    addEventListener(name, fn){this.events.set(name,fn);},
    showModal(){modalCalls++;this.open=true;},
    close(){this.open=false;this.events.get('close')();},
};
pageEvents.get('app:content-loaded')();
pageEvents.get('app:content-loaded')(); // initialization must be idempotent
let prevented=false;
pictures[0].events.get('click')({preventDefault(){prevented=true;}});
assert.equal(prevented,true);
assert.equal(modalCalls,1);
assert.equal(currentViewer.open,true);
assert.equal(controls.img.src,'/storage/photo-0.jpg');
assert.equal(focused,controls['[data-gallery-close]']);
currentViewer.events.get('keydown')({key:'ArrowRight',preventDefault(){}});
assert.equal(controls.img.src,'/storage/photo-1.jpg');
controls['[data-gallery-close]'].events.get('click')();
assert.equal(currentViewer.open,false);
assert.equal(focused,pictures[0],'Closing restores the opener without scrolling');
const galleryTemplate=await readFile(new URL('../views/frontend/next/gallery-detail.php',import.meta.url),'utf8');
assert.match(galleryTemplate,/<button[^>]+data-gallery-src=/);
assert.doesNotMatch(galleryTemplate,/<a[^\n]*data-gallery-image/);
assert.match(await readFile(new URL('../public/js/app.js',import.meta.url),'utf8'),/dispatchEvent\(new CustomEvent\('app:content-loaded'\)\)/);
console.log('PASS: SPA gallery initialization, modal open/close, keyboard next and focus restoration; no photo links/new tabs.');

const adminSource=await readFile(new URL('../public/js/admin.js',import.meta.url),'utf8');
const sidebarCode=adminSource.slice(adminSource.indexOf('/* Admin navigation state:'));
const storage=new Map([['rahayat-admin-navigation-v1',JSON.stringify({top:380,focusPath:'/admin/pengaturan'})]]);
const navEvents=new Map();
const activeLink={href:'http://localhost/admin/pengaturan',pathname:'/admin/pengaturan',
    setAttribute(name,value){this[name]=value;},getBoundingClientRect:()=>({top:100,bottom:150}),
    focus(options){assert.equal(options.preventScroll,true);focused=this;}};
const sidebar={scrollTop:0,querySelectorAll:()=>[activeLink],contains:link=>link===activeLink,
    getBoundingClientRect:()=>({top:0,bottom:500}),addEventListener(name,fn){navEvents.set(name,fn);}};
vm.runInNewContext(sidebarCode,{
    document:{addEventListener(name,fn){if(name==='DOMContentLoaded')fn();},querySelector:selector=>selector==='#sidebar nav'?sidebar:null},
    window:{location:{pathname:'/admin/pengaturan',origin:'http://localhost'},addEventListener(){}},URL,
    sessionStorage:{getItem:key=>storage.get(key),setItem:(key,value)=>storage.set(key,value)},
});
assert.equal(sidebar.scrollTop,380);
assert.equal(activeLink['aria-current'],'page');
assert.equal(focused,activeLink);
sidebar.scrollTop=410;
navEvents.get('click')({target:{closest:()=>activeLink},button:0});
assert.deepEqual(JSON.parse(storage.get('rahayat-admin-navigation-v1')),{top:410,focusPath:'/admin/pengaturan'});
console.log('PASS: admin sidebar scroll retention, active focus without page jump, and navigation-state persistence.');
