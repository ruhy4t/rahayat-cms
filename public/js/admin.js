/**
 * ============================================
 * SchoolWeb CMS - Admin JavaScript
 * AJAX CRUD Operations
 * ============================================
 */

(function () {
    'use strict';

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ========================================
    // News CRUD Operations
    // ========================================
    let currentNewsId = null;
    let deleteNewsId = null;

    // Open news modal for create
    window.openNewsModal = function () {
        currentNewsId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Berita';
        document.getElementById('newsForm').reset();
        document.getElementById('newsId').value = '';

        // Reset editor if helper function exists (works with Summernote or Quill)
        if (typeof resetEditor === 'function') {
            resetEditor();
        } else if (typeof quill !== 'undefined') {
            quill.root.innerHTML = '';
        }

        document.getElementById('newsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    // Close news modal
    window.closeNewsModal = function () {
        const modal = document.getElementById('newsModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Edit news
    window.editNews = async function (id) {
        currentNewsId = id;
        document.getElementById('modalTitle').textContent = 'Edit Berita';

        try {
            const response = await fetch(`/api/news/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success && result.data) {
                const news = result.data;
                document.getElementById('newsId').value = news.id;
                document.getElementById('title').value = news.title || '';

                // Handle category - try category_id first, then fallback to category
                const categorySelect = document.getElementById('category_id');
                if (categorySelect) {
                    categorySelect.value = news.category_id || '';
                }

                document.getElementById('status').value = news.status || 'draft';
                document.getElementById('excerpt').value = news.excerpt || '';

                // Sync editor with content (works with Summernote or Quill)
                if (typeof setEditorContent === 'function') {
                    setEditorContent(news.content || '');
                } else if (typeof quill !== 'undefined') {
                    quill.root.innerHTML = news.content || '';
                }

                document.getElementById('newsModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                showToast('Gagal memuat data berita', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    };

    // Save news (create or update)
    window.saveNews = async function () {
        const form = document.getElementById('newsForm');
        const saveBtn = document.getElementById('saveBtn');
        const saveBtnText = document.getElementById('saveBtnText');
        const saveBtnLoading = document.getElementById('saveBtnLoading');

        // Get content from editor (Summernote or Quill)
        let content = '';
        if (typeof getEditorContent === 'function') {
            content = getEditorContent();
        } else if (typeof quill !== 'undefined') {
            content = quill.root.innerHTML;
        } else {
            // Fallback to textarea or hidden field
            const contentField = document.getElementById('summernoteEditor') || document.getElementById('content');
            content = contentField ? contentField.value : '';
        }

        // Strip empty HTML tags for proper validation
        const strippedContent = content.replace(/<[^>]*>?/gm, '').trim();

        // Validate
        const title = document.getElementById('title').value.trim();

        if (!title) {
            showToast('Judul harus diisi', 'error');
            document.getElementById('title').focus();
            return;
        }

        if (!strippedContent) {
            showToast('Konten harus diisi', 'error');
            return;
        }

        // Show loading
        saveBtn.disabled = true;
        saveBtnText.classList.add('hidden');
        saveBtnLoading.classList.remove('hidden');

        // Prepare form data
        const formData = new FormData(form);
        formData.append(getCsrfTokenName(), csrfToken);

        const url = currentNewsId ? `/api/news/update/${currentNewsId}` : '/api/news/store';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message || 'Berhasil!', 'success');
                closeNewsModal();

                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showToast(result.message || 'Gagal menyimpan', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtnText.classList.remove('hidden');
            saveBtnLoading.classList.add('hidden');
        }
    };

    // Delete news - show confirmation with SweetAlert2
    window.deleteNews = function (id) {
        Swal.fire({
            title: 'Hapus berita ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append(getCsrfTokenName(), csrfToken);

                    const response = await fetch(`/api/news/delete/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const resultData = await response.json();

                    if (resultData.success) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: resultData.message || 'Berita berhasil dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Remove row from table
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            setTimeout(() => row.remove(), 300);
                        }
                    } else {
                        showToast(resultData.message || 'Gagal menghapus', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'error');
                }
            }
        });
    };

    // Deprecated modal functions (kept empty or removed to avoid errors if referenced)
    window.closeDeleteModal = function () { };
    window.confirmDelete = function () { };

    // ========================================
    // Toast Notification (SweetAlert2 Wrapper)
    // ========================================
    function showToast(message, type = 'success') {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 not loaded');
            alert(message);
            return;
        }

        const icon = type === 'success' ? 'success' : 'error';
        const title = type === 'success' ? 'Berhasil!' : 'Gagal!';

        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    // ========================================
    // Utility Functions
    // ========================================
    function getCsrfTokenName() {
        return '_csrf_token';
    }

    // Close modals on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeNewsModal();
            closeDeleteModal();
        }
    });

    // Add transition styles for table rows
    const style = document.createElement('style');
    style.textContent = `
        tr[data-id] {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    `;
    document.head.appendChild(style);

})();
