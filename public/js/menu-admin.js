/**
 * Menu management interactions with automatic CSRF token renewal.
 */
(function () {
    'use strict';

    const modal = document.getElementById('menuModal');
    const form = document.getElementById('menuForm');
    const menuList = document.getElementById('menuList');

    if (!modal || !form || !menuList) return;

    function currentCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || form.querySelector('input[name="csrf_token"]')?.value
            || '';
    }

    function updateCsrfToken(token) {
        if (!token) return;

        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.content = token;

        document.querySelectorAll('input[name="csrf_token"]').forEach((input) => {
            input.value = token;
        });
    }

    async function renewCsrfToken() {
        const response = await fetch('/api/csrf-token', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        const result = await response.json();
        const token = result?.data?.token;

        if (!response.ok || !result.success || !token) {
            throw new Error(response.status === 401
                ? 'Sesi login telah berakhir. Silakan masuk kembali.'
                : 'Token keamanan tidak dapat diperbarui. Muat ulang halaman.');
        }

        updateCsrfToken(token);
        return token;
    }

    async function csrfRequest(url, { formData = null, json = null } = {}) {
        for (let attempt = 0; attempt < 2; attempt += 1) {
            const token = currentCsrfToken();
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            };
            let body;

            if (formData) {
                formData.set('csrf_token', token);
                body = formData;
            } else {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify({ ...(json || {}), csrf_token: token });
            }

            const response = await fetch(url, {
                method: 'POST',
                headers,
                body,
                credentials: 'same-origin'
            });
            let result;

            try {
                result = await response.json();
            } catch (error) {
                throw new Error('Respons server tidak dapat dibaca. Silakan coba kembali.');
            }

            const csrfExpired = response.status === 403
                && /csrf|token keamanan/i.test(String(result?.message || ''));

            if (csrfExpired && attempt === 0) {
                await renewCsrfToken();
                continue;
            }

            return { response, result };
        }

        throw new Error('Sesi keamanan tidak dapat diperbarui. Muat ulang halaman.');
    }

    function setSubmitBusy(isBusy) {
        const button = form.querySelector('button[type="submit"]');
        if (!button) return;

        button.disabled = isBusy;
        button.classList.toggle('opacity-60', isBusy);
        button.classList.toggle('cursor-not-allowed', isBusy);
    }

    if (typeof Sortable !== 'undefined') {
        new Sortable(menuList, {
            animation: 150,
            ghostClass: 'bg-primary-50',
            onEnd: async function () {
                const order = [...document.querySelectorAll('#menuList li')]
                    .map((item) => item.dataset.id)
                    .filter(Boolean);
                if (!order.length) return;

                try {
                    const { result } = await csrfRequest('/admin/menu/reorder', { json: { order } });
                    if (!result.success) {
                        alert(result.message || 'Gagal menyimpan urutan menu');
                        location.reload();
                    }
                } catch (error) {
                    alert(error.message || 'Gagal menyimpan urutan menu');
                    location.reload();
                }
            }
        });
    }

    window.openModal = function () {
        document.getElementById('modalTitle').textContent = 'Tambah Menu';
        form.action = '/admin/menu/store';
        form.reset();
        document.getElementById('menuId').value = '';
        resetParentOptions();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.editMenu = function (menu) {
        document.getElementById('modalTitle').textContent = 'Edit Menu';
        form.action = '/admin/menu/update/' + menu.id;
        document.getElementById('menuId').value = menu.id;
        resetParentOptions(menu.id);
        document.getElementById('menuTitle').value = menu.title;
        document.getElementById('menuUrl').value = menu.url;
        document.getElementById('menuParent').value = menu.parent_id || '';
        document.getElementById('menuLocation').value = menu.menu_location;
        document.getElementById('menuTarget').value = menu.target;
        document.getElementById('menuActive').checked = menu.is_active == 1;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeModal = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    function resetParentOptions(currentId = null) {
        document.querySelectorAll('#menuParent option').forEach((option) => {
            option.disabled = currentId !== null && option.value === String(currentId);
        });
    }

    window.deleteMenu = async function (id, title) {
        if (!confirm('Hapus menu "' + title + '"?')) return;

        try {
            const { result } = await csrfRequest('/admin/menu/delete/' + id);
            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Gagal menghapus');
            }
        } catch (error) {
            alert(error.message || 'Gagal menghapus');
        }
    };

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        setSubmitBusy(true);

        try {
            const { result } = await csrfRequest(form.action, {
                formData: new FormData(form)
            });
            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Gagal menyimpan');
            }
        } catch (error) {
            alert(error.message || 'Gagal menyimpan');
        } finally {
            setSubmitBusy(false);
        }
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) window.closeModal();
    });
})();
