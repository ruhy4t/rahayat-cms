import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../public/js/security-migration.js', import.meta.url), 'utf8');
function setup(responses) {
    let submit;
    let requests = 0;
    let active = 0;
    let peak = 0;
    const tokens = [];
    const button = { disabled: false, textContent: '' };
    const csrf = { value: 'first-token' };
    const progress = { textContent: '' };
    const backup = { textContent: '' };
    const form = {
        action: '/admin/pembaruan/migrasi/run',
        querySelector: (selector) => selector.startsWith('button') ? button : csrf,
        reportValidity: () => true,
        setAttribute: () => {},
        addEventListener: (name, callback) => { submit = callback; }
    };
    const context = {
        document: { getElementById: (id) => ({ 'security-migration-form': form, 'security-migration-progress': progress, 'security-migration-backup': backup })[id] },
        window: { addEventListener: () => {} },
        FormData: class { constructor() { tokens.push(csrf.value); } },
        AbortController,
        setTimeout: (fn, ms) => ms === 300 ? setTimeout(fn, 0) : setTimeout(fn, ms),
        clearTimeout,
        fetch: async (url, options) => {
            assert.equal(url, form.action);
            assert.equal(options.method, 'POST');
            assert.equal(options.headers['X-Requested-With'], 'XMLHttpRequest');
            active++;
            peak = Math.max(peak, active);
            const next = responses[requests++];
            await new Promise((resolve) => setImmediate(resolve));
            active--;
            if (next instanceof Error) throw next;
            assert.ok(next, 'Unexpected extra migration request');
            return {
                redirected: false, status: 200, ok: true,
                headers: { get: () => 'application/json' },
                json: async () => next,
                ...next.response
            };
        }
    };
    vm.runInNewContext(source, context);
    return { run: () => submit({ preventDefault() {} }), button, progress, backup, tokens,
        requests: () => requests, peak: () => peak };
}
const batch = (complete) => ({ success: true, csrf_token: 'renewed-token', result: {
    complete, rows: 2, documents: 1, news: 0, backup: 'storage/backups/synthetic'
} });
const successful = setup([batch(false), batch(false), batch(true)]);
await Promise.all([successful.run(), successful.run()]);
assert.equal(successful.requests(), 3, 'One click must finish every batch; double click must not duplicate');
assert.equal(successful.peak(), 1, 'Requests must be sequential');
assert.deepEqual(successful.tokens, ['first-token', 'renewed-token', 'renewed-token']);
assert.match(successful.progress.textContent, /Migrasi selesai.*6 pendaftar, 3 dokumen/);
assert.equal(successful.button.disabled, false);

const retry = setup([new Error('Network failed'), batch(true)]);
await retry.run();
assert.equal(retry.requests(), 1, 'Failure must not start an automatic retry loop');
assert.equal(retry.button.textContent, 'Coba lagi');
await retry.run();
assert.match(retry.progress.textContent, /Migrasi selesai/);

const expired = setup([{ response: { status: 403, ok: false } }]);
await expired.run();
assert.match(expired.progress.textContent, /login kembali/);
assert.equal(expired.requests(), 1);

const invalid = setup([{ success: true, result: {} }]);
await invalid.run();
assert.match(invalid.progress.textContent, /Hasil migrasi tidak valid/);
assert.equal(invalid.requests(), 1);

const serverError = setup([{ success: false, message: 'Backup gagal', response: { status: 500, ok: false } }]);
await serverError.run();
assert.match(serverError.progress.textContent, /Backup gagal/);
assert.equal(serverError.button.disabled, false);
console.log('PASS: automatic batches, double-click guard, CSRF renewal, completion, retry, expired session, malformed response, server failure.');
