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
