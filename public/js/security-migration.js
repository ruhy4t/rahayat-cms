(() => {
    'use strict';
    const form = document.getElementById('security-migration-form');
    if (!form) return;
    const button = form.querySelector('button[type="submit"]');
    const progress = document.getElementById('security-migration-progress');
    const backup = document.getElementById('security-migration-backup');
    let running = false;

    window.addEventListener('beforeunload', (event) => {
        if (running) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (running || !form.reportValidity()) return;
        running = true;
        button.disabled = true;
        button.textContent = 'Migrasi sedang berjalan…';
        form.setAttribute('aria-busy', 'true');
        let batches = 0;
        let rows = 0;
        let documents = 0;
        let news = 0;
        try {
            while (true) {
                progress.textContent = `Memproses tahap ${batches + 1}. Sudah diperbarui: ${rows} pendaftar, ${documents} dokumen, ${news} berita. Jangan tutup halaman ini.`;
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 60000);
                let payload;
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                        credentials: 'same-origin',
                        signal: controller.signal
                    });
                    if (response.redirected || response.status === 401 || response.status === 403) {
                        throw new Error('Sesi berakhir. Muat ulang halaman dan login kembali untuk melanjutkan.');
                    }
                    if (!(response.headers.get('content-type') || '').includes('application/json')) {
                        throw new Error('Respons hosting tidak terbaca. Periksa koneksi lalu klik Coba lagi.');
                    }
                    payload = await response.json();
                    if (!response.ok || payload.success !== true) {
                        throw new Error(payload.message || 'Tahap gagal. Periksa hosting lalu klik Coba lagi.');
                    }
                } finally {
                    clearTimeout(timeout);
                }
                const result = payload.result;
                if (!result || typeof result.complete !== 'boolean'
                    || !['rows', 'documents', 'news'].every((key) => Number.isInteger(result[key]) && result[key] >= 0)) {
                    throw new Error('Hasil migrasi tidak valid. Muat ulang halaman untuk melanjutkan.');
                }
                if (payload.csrf_token) {
                    const csrf = form.querySelector('input[type="hidden"]');
                    csrf.value = payload.csrf_token;
                }
                batches++;
                rows += result.rows;
                documents += result.documents;
                news += result.news;
                if (result.backup) backup.textContent = `Backup tahap terakhir terverifikasi: ${result.backup}`;
                const counts = `${batches} tahap selesai: ${rows} pendaftar, ${documents} dokumen, ${news} berita diperbarui pada proses ini.`;
                progress.textContent = counts;
                if (result.complete) {
                    progress.textContent = `Migrasi selesai. ${counts} Simpan semua folder backup migrasi melalui File Manager.`;
                    button.textContent = 'Periksa kembali';
                    break;
                }
                await new Promise((resolve) => setTimeout(resolve, 300));
            }
        } catch (error) {
            const message = error.name === 'AbortError'
                ? 'Waktu tunggu habis. Tahap terakhir mungkin masih diproses hosting. Tunggu sebentar lalu klik Coba lagi.'
                : error instanceof TypeError ? 'Koneksi terputus. Periksa koneksi lalu klik Coba lagi.' : error.message;
            progress.textContent = `${message} Data yang sudah dienkripsi tetap tersimpan dan akan dilewati saat melanjutkan.`;
            button.textContent = 'Coba lagi';
        } finally {
            running = false;
            button.disabled = false;
            form.setAttribute('aria-busy', 'false');
        }
    });
})();
