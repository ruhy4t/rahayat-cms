import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

function element(dataset = {}) {
    return { dataset, hidden: false, textContent: '', attributes: {}, handlers: {},
        setAttribute(name, value) { this.attributes[name] = value; },
        addEventListener(name, callback) { this.handlers[name] = callback; } };
}
const events = [element({eventStart: '2026-09-08', eventEnd: '2026-09-12'}), element({eventStart: '2026-09-12', eventEnd: '2026-09-12'})];
const buttons = ['2026-09-08', '2026-09-12', '2026-09-13'].map(calendarDate => element({calendarDate}));
const heading = element(), empty = element(), reset = element();
const calendar = {
    querySelector: key => ({'[data-calendar-heading]': heading, '[data-calendar-empty]': empty, '[data-calendar-reset]': reset})[key],
    querySelectorAll: key => key === '[data-event-start]' ? events : buttons,
};
vm.runInNewContext(readFileSync(new URL('../public/js/academic-calendar.js', import.meta.url), 'utf8'), {document: {querySelectorAll: () => [calendar]}, Intl, Date});
buttons[0].handlers.click();
assert.equal(events[0].hidden, false); assert.equal(events[1].hidden, true);
assert.equal(buttons[0].attributes['aria-pressed'], 'true');
buttons[1].handlers.click();
assert.ok(events.every(event => !event.hidden));
buttons[2].handlers.click();
assert.ok(events.every(event => event.hidden)); assert.equal(empty.hidden, false);
assert.match(heading.textContent, /13 September 2026/);
reset.handlers.click();
assert.ok(events.every(event => !event.hidden)); assert.equal(reset.hidden, true);
console.log('PASS: calendar date selection, overlapping events, empty date, reset and accessible selection.');
