<?php
$isEdit = isset($news);
$item = $isEdit ? $news : [];
$currentUser = $data['user'] ?? $user ?? [];
$isRestricted = in_array($currentUser['role'] ?? '', ['murid', 'ekskul']);
$editorUploadBatch = Security::randomString(32);
?>

<!-- CKEditor 5 Superbuild (Includes all features + Table Resize) -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/super-build/ckeditor.js"></script>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden">
    <form id="newsForm" enctype="multipart/form-data">
        <?= Security::csrfInput() ?>
        <input type="hidden" id="editorEmbedsJson" name="editor_embeds_json" value="[]">
        <input type="hidden" id="removedEditorEmbedsJson" name="removed_editor_embeds_json" value="[]">
        <input type="hidden" id="editorUploadBatch" name="editor_upload_batch" value="<?= e($editorUploadBatch) ?>">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Column: Main Content -->
            <div class="flex-1 p-6 space-y-6 border-b lg:border-b-0 lg:border-r border-slate-200">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Judul Berita</label>
                    <input type="text" id="title" name="title" value="<?= e($item['title'] ?? '') ?>" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-all font-medium text-lg placeholder:text-slate-400"
                        placeholder="Masukkan judul berita menarik...">
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-slate-700 mb-2">Kutipan Singkat
                        (Excerpt)</label>
                    <textarea id="excerpt" name="excerpt" rows="3"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-all resize-none text-slate-600"
                        placeholder="Ringkasan singkat berita untuk ditampilkan di kartu berita..."><?= e($item['excerpt'] ?? '') ?></textarea>
                    <p class="text-xs text-slate-500 mt-1">Ditampilkan di halaman depan dan daftar berita.</p>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-2">Isi Berita</label>
                    <div class="prose max-w-none">
                        <textarea id="content" name="content"><?= $item['content'] ?? '' ?></textarea>
                    </div>
                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                        <input type="file" id="pdfUpload" accept="application/pdf" class="hidden">
                        <button type="button" id="pdfUploadBtn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Unggah PDF ke Isi Berita
                        </button>
                        <span id="pdfUploadStatus" class="text-xs text-slate-500">PDF maks. 10MB.</span>
                    </div>
                    <div id="pdfEmbedManager" class="hidden mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <div class="text-xs font-semibold text-slate-600 mb-2">PDF di isi berita</div>
                        <div id="pdfEmbedList" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Meta & Settings -->
            <div class="w-full lg:w-96 bg-slate-50 p-6 space-y-8 h-fit">
                <!-- Publishing Status -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status Publikasi
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                            <?php if ($isRestricted): ?>
                                <input type="hidden" name="status" value="draft">
                                <div
                                    class="px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
                                    <span class="font-medium">Draft</span> — Berita Anda perlu disetujui oleh Admin/GTK
                                    untuk diterbitkan
                                </div>
                            <?php else: ?>
                                <select name="status"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                    <option value="published" <?= ($item['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                                        Published (Terbit)</option>
                                    <option value="draft" <?= ($item['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft
                                        (Konsep)</option>
                                    <option value="archived" <?= ($item['status'] ?? '') === 'archived' ? 'selected' : '' ?>>
                                        Archived (Arsip)</option>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                            <select name="category_id" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                    $selected = '';
                                    if (isset($item['category_id']) && $item['category_id'] == $cat['id']) {
                                        $selected = 'selected';
                                    } elseif (isset($item['category']) && strtolower($item['category']) === strtolower($cat['name'])) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Gambar Utama
                    </h3>

                    <div class="space-y-4">
                        <!-- Preview Container -->
                        <div id="imagePreviewContainer"
                            class="<?= empty($item['image']) ? 'hidden' : '' ?> relative aspect-video rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                            <img id="imagePreview"
                                src="<?= !empty($item['image']) ? '/storage/' . e($item['image']) : '#' ?>"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Upload Input -->
                        <div class="relative">
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors cursor-pointer">
                        </div>
                        <p class="text-xs text-slate-500">Format: JPG, PNG, GIF, WebP. Maks: 5MB.</p>
                    </div>
                </div>

                <!-- SEO Meta -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        SEO Meta
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" rows="3"
                                class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-none"><?= e($item['meta_description'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Meta Keywords / Tag</label>
                            <input type="text" name="meta_keywords" value="<?= e($item['meta_keywords'] ?? '') ?>"
                                class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                placeholder="Komputer, Prestasi, dsb">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer for Actions -->
        <div
            class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row gap-3 items-center justify-end sticky bottom-0 z-10">
            <a href="/admin/berita"
                class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-slate-600 font-medium hover:bg-slate-200 transition-colors text-center">
                Batal
            </a>
            <button type="submit" id="submitBtn"
                class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:shadow-lg hover:shadow-primary-500/30 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span id="submitText"><?= $isEdit ? 'Perbarui Berita' : 'Simpan Berita' ?></span>
            </button>
        </div>
    </form>
</div>

<!-- Script Handling -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfFieldName = '<?= CSRF_TOKEN_NAME ?>';
        const editorUploadBatch = document.getElementById('editorUploadBatch')?.value || '';

        function getCsrfToken() {
            const csrfInput = document.querySelector(`input[name="${csrfFieldName}"]`);
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

            return csrfInput?.value || csrfTokenMeta?.getAttribute('content') || '';
        }

        function updateCsrfToken(token) {
            const csrfInput = document.querySelector(`input[name="${csrfFieldName}"]`);
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

            if (csrfInput) csrfInput.value = token;
            if (csrfTokenMeta) csrfTokenMeta.setAttribute('content', token);
        }

        async function refreshCsrfToken() {
            let csrfToken = getCsrfToken();

            try {
                const tokenResponse = await fetch('/api/csrf-token', {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                const tokenPayload = await tokenResponse.json();
                const freshToken = tokenPayload?.data?.token;
                if (freshToken) {
                    csrfToken = freshToken;
                    updateCsrfToken(freshToken);
                }
            } catch (tokenError) {
                console.warn('CSRF token refresh failed, using current page token.', tokenError);
            }

            return csrfToken;
        }

        // Custom Upload Adapter
        const editorEmbeds = [];
        const removedEditorEmbeds = new Set();

        function rememberEditorEmbed(embed) {
            if (!embed?.url || removedEditorEmbeds.has(embed.url) || editorEmbeds.some(item => item.url === embed.url)) {
                return;
            }

            editorEmbeds.push(embed);
            const embedsInput = document.getElementById('editorEmbedsJson');
            if (embedsInput) embedsInput.value = JSON.stringify(editorEmbeds);
        }

        function rememberRemovedEditorEmbed(url) {
            if (!url) return;

            removedEditorEmbeds.add(url);
            const removedInput = document.getElementById('removedEditorEmbedsJson');
            if (removedInput) removedInput.value = JSON.stringify([...removedEditorEmbeds]);
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        function normalizeEditorMediaUrl(url) {
            if (!url) return '';

            let normalized = String(url).trim();
            try {
                normalized = new URL(normalized, window.location.origin).pathname;
            } catch (error) {
                normalized = normalized.replace(/^(\.\/|\.\.\/)+/, '');
            }

            if (normalized.startsWith('/storage/uploads/news/')) return normalized;
            if (normalized.startsWith('storage/uploads/news/')) return '/' + normalized;
            if (normalized.startsWith('/uploads/news/')) return '/storage' + normalized;
            if (normalized.startsWith('uploads/news/')) return '/storage/' + normalized;

            return '';
        }

        function collectVisibleEditorMedia() {
            document.querySelectorAll('.ck-editor__editable img').forEach((img) => {
                const url = normalizeEditorMediaUrl(img.getAttribute('src') || img.currentSrc);
                if (!url) return;

                rememberEditorEmbed({
                    type: 'image',
                    url: url,
                    title: img.getAttribute('alt') || 'Gambar berita'
                });
            });

            document.querySelectorAll('.ck-editor__editable iframe').forEach((iframe) => {
                const url = normalizeEditorMediaUrl(iframe.getAttribute('src'));
                if (!url || !url.toLowerCase().endsWith('.pdf')) return;

                rememberEditorEmbed({
                    type: 'pdf',
                    url: url,
                    title: iframe.getAttribute('title') || 'Dokumen PDF'
                });
            });
        }

        function getEditorHtml() {
            if (window.editorInstance) return window.editorInstance.getData();
            return document.getElementById('content')?.value || '';
        }

        function setEditorHtml(html) {
            if (window.editorInstance) {
                window.editorInstance.setData(html);
                return;
            }

            const textarea = document.getElementById('content');
            if (textarea) textarea.value = html;
        }

        function extractPdfEmbeds(html) {
            const pdfs = new Map();
            const pattern = /\b(?:src|href)=["']([^"']+\.pdf(?:\?[^"']*)?)["'][^>]*?(?:title=["']([^"']*)["'])?/gi;
            let match;

            while ((match = pattern.exec(html)) !== null) {
                const url = normalizeEditorMediaUrl(match[1]);
                if (!url || removedEditorEmbeds.has(url)) continue;

                const title = match[2] || decodeURIComponent((url.split('/').pop() || 'Dokumen PDF').replace(/\.pdf(?:\?.*)?$/i, ''));
                pdfs.set(url, title);
            }

            return [...pdfs.entries()].map(([url, title]) => ({ url, title }));
        }

        function removePdfFromHtml(html, url) {
            const escapedUrl = url.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const relativeUrl = escapedUrl.replace(/^\\\/storage\\\//, '\\/?storage/');
            const urlPattern = `(?:${escapedUrl}|${relativeUrl})`;

            html = html.replace(new RegExp(`<figure\\b[^>]*>[\\s\\S]*?${urlPattern}[\\s\\S]*?<\\/figure>`, 'gi'), '');
            html = html.replace(new RegExp(`<p>\\s*<iframe\\b[^>]*(?:src=["']${urlPattern}["'])[^>]*>\\s*<\\/iframe>\\s*<\\/p>`, 'gi'), '');
            html = html.replace(new RegExp(`<iframe\\b[^>]*(?:src=["']${urlPattern}["'])[^>]*>\\s*<\\/iframe>`, 'gi'), '');
            html = html.replace(new RegExp(`<a\\b[^>]*(?:href=["']${urlPattern}["'])[^>]*>[\\s\\S]*?<\\/a>`, 'gi'), '');

            return html;
        }

        function renderPdfEmbedManager() {
            const manager = document.getElementById('pdfEmbedManager');
            const list = document.getElementById('pdfEmbedList');
            if (!manager || !list) return;

            const pdfs = extractPdfEmbeds(getEditorHtml());
            manager.classList.toggle('hidden', pdfs.length === 0);
            list.innerHTML = '';

            pdfs.forEach(pdf => {
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-3 rounded-md bg-white border border-slate-200 px-3 py-2';
                row.innerHTML = `
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-slate-700 truncate">${escapeHtml(pdf.title || 'Dokumen PDF')}</div>
                        <div class="text-xs text-slate-400 truncate">${escapeHtml(pdf.url)}</div>
                    </div>
                    <button type="button" class="shrink-0 px-3 py-1.5 rounded-md bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100">Hapus</button>
                `;
                row.querySelector('button').addEventListener('click', () => {
                    rememberRemovedEditorEmbed(pdf.url);
                    setEditorHtml(removePdfFromHtml(getEditorHtml(), pdf.url));
                    renderPdfEmbedManager();
                });
                list.appendChild(row);
            });
        }

        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file
                    .then(file => refreshCsrfToken().then(csrfToken => {
                        return new Promise((resolve, reject) => {
                            this._initRequest();
                            this._initListeners(resolve, reject, file);
                            this._sendRequest(file, csrfToken);
                        });
                    }));
            }

            abort() {
                if (this.xhr) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                const xhr = this.xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/upload/image', true);
                xhr.responseType = 'json';
            }

            _initListeners(resolve, reject, file) {
                const xhr = this.xhr;
                const loader = this.loader;
                const genericErrorText = `Couldn't upload file: ${file.name}.`;

                xhr.addEventListener('error', () => reject(genericErrorText));
                xhr.addEventListener('abort', () => reject());
                xhr.addEventListener('load', () => {
                    const response = xhr.response;

                    if (!response || response.error) {
                        return reject(response && response.error ? response.error.message : genericErrorText);
                    }

                    const url = response.url || response.default;
                    if (!url) {
                        return reject(genericErrorText);
                    }

                    if (url) {
                        rememberEditorEmbed({
                            type: 'image',
                            url: url,
                            title: file.name || 'Gambar berita'
                        });
                    }

                    resolve({ default: url });
                });

                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', evt => {
                        if (evt.lengthComputable) {
                            loader.uploadTotal = evt.total;
                            loader.uploaded = evt.loaded;
                        }
                    });
                }
            }

            _sendRequest(file, csrfToken) {
                const data = new FormData();
                data.append('upload', file);
                if (editorUploadBatch) data.append('editor_upload_batch', editorUploadBatch);

                if (csrfToken) {
                    data.append(csrfFieldName, csrfToken);
                    this.xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }

                this.xhr.send(data);
            }
        }

        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
        }

        // Initialize CKEditor 5 Superbuild
        if (window.CKEDITOR && window.CKEDITOR.ClassicEditor) {
            CKEDITOR.ClassicEditor.create(document.querySelector('#content'), {
                // Plugins configuration
                extraPlugins: [MyCustomUploadAdapterPlugin],

                // Toolbar configuration
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'link', '|',
                        'undo', 'redo',
                        '-',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                        'alignment', '|',
                        'horizontalLine', 'pageBreak', '|',
                        'sourceEditing'
                    ],
                    shouldNotGroupWhenFull: true
                },

                // Language
                language: 'id',

                // Image configuration
                image: {
                    toolbar: [
                        'imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                        'toggleImageCaption', 'imageResize'
                    ],
                    insert: {
                        type: 'auto'
                    }
                },

                // Table configuration (Enable resizing)
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'
                    ]
                },

                htmlSupport: {
                    allow: [
                        { name: 'figure', classes: true, styles: true, attributes: true },
                        { name: 'figcaption', classes: true, styles: true, attributes: true },
                        { name: 'iframe', classes: true, styles: true, attributes: ['src', 'title', 'loading', 'allow', 'allowfullscreen', 'frameborder'] },
                        { name: 'img', classes: true, styles: true, attributes: true }
                    ]
                },

                // Remove premium/AI plugins to avoid license errors
                removePlugins: ['CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord', 'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced'],
            })
                .then(editor => {
                    window.editorInstance = editor;
                    console.log('CKEditor Superbuild initialized successfully');

                    // Set initial height
                    editor.editing.view.change(writer => {
                        writer.setStyle('min-height', '400px', editor.editing.view.document.getRoot());
                    });

                    renderPdfEmbedManager();
                    editor.model.document.on('change:data', () => {
                        window.clearTimeout(window.__newsPdfRenderTimer);
                        window.__newsPdfRenderTimer = window.setTimeout(renderPdfEmbedManager, 250);
                    });
                })
                .catch(err => {
                    console.error('CKEditor Init Error:', err);
                    // Fallback
                    document.querySelector('#content').style.display = 'block';
                });
        }

        // 2. Image Preview Logic
        const imageInput = document.getElementById('image');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const previewImage = document.getElementById('imagePreview');

        if (imageInput && previewContainer && previewImage) {
            imageInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        previewImage.src = ev.target.result;
                        previewContainer.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // 3. PDF Upload Logic
        const pdfInput = document.getElementById('pdfUpload');
        const pdfButton = document.getElementById('pdfUploadBtn');
        const pdfStatus = document.getElementById('pdfUploadStatus');
        function insertHtmlToEditor(html) {
            if (window.editorInstance) {
                try {
                    const viewFragment = window.editorInstance.data.processor.toView(html);
                    const modelFragment = window.editorInstance.data.toModel(viewFragment);
                    window.editorInstance.model.insertContent(modelFragment, window.editorInstance.model.document.selection);
                    return;
                } catch (error) {
                    console.warn('Insert PDF embed failed, appending to editor data.', error);
                    window.editorInstance.setData(window.editorInstance.getData() + html);
                    return;
                }
            }

            const textarea = document.getElementById('content');
            if (textarea) textarea.value += html;
        }

        if (pdfInput && pdfButton) {
            pdfButton.addEventListener('click', () => pdfInput.click());
            pdfInput.addEventListener('change', async function () {
                const file = this.files?.[0];
                if (!file) return;

                if (file.type !== 'application/pdf') {
                    showNotification('error', 'File harus berformat PDF.');
                    this.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    showNotification('error', 'Ukuran PDF maksimal 10MB.');
                    this.value = '';
                    return;
                }

                pdfButton.disabled = true;
                if (pdfStatus) pdfStatus.textContent = 'Mengunggah PDF...';

                try {
                    const csrfToken = await refreshCsrfToken();
                    const formData = new FormData();
                    formData.append('pdf', file);
                    formData.append(csrfFieldName, csrfToken);
                    if (editorUploadBatch) formData.append('editor_upload_batch', editorUploadBatch);

                    const response = await fetch('/admin/upload/pdf', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: formData
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Gagal mengunggah PDF.');
                    }

                    insertHtmlToEditor(result.embedHtml);
                    rememberEditorEmbed({
                        type: 'pdf',
                        url: result.url,
                        title: file.name.replace(/\.pdf$/i, '')
                    });
                    renderPdfEmbedManager();
                    showNotification('success', 'PDF berhasil ditambahkan ke isi berita.');
                    if (pdfStatus) pdfStatus.textContent = file.name;
                } catch (error) {
                    console.error('PDF Upload Error:', error);
                    showNotification('error', error.message || 'Gagal mengunggah PDF.');
                    if (pdfStatus) pdfStatus.textContent = 'PDF maks. 10MB.';
                } finally {
                    pdfButton.disabled = false;
                    this.value = '';
                }
            });
        }

        // 4. Form Submission Logic
        const form = document.getElementById('newsForm');
        if (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Sync CKEditor data to textarea
                let content = '';
                collectVisibleEditorMedia();

                if (window.editorInstance) {
                    content = window.editorInstance.getData();
                } else {
                    content = document.getElementById('content').value;
                }
                editorEmbeds.forEach(embed => {
                    if (embed.url && !removedEditorEmbeds.has(embed.url) && !content.includes(embed.url)) {
                        if (embed.type === 'pdf') {
                            const title = escapeHtml(embed.title || 'Dokumen PDF');
                            content += `<figure class="pdf-embed"><iframe src="${embed.url}" title="${title}" loading="lazy"></iframe><figcaption>${title}</figcaption></figure>`;
                        } else if (embed.type === 'image') {
                            const title = escapeHtml(embed.title || 'Gambar berita');
                            content += `<figure class="image"><img src="${embed.url}" alt="${title}"></figure>`;
                        }
                    }
                });

                const csrfToken = await refreshCsrfToken();

                const formData = new FormData(this);
                formData.set('content', content);
                formData.set(csrfFieldName, csrfToken);
                formData.set('removed_editor_embeds_json', JSON.stringify([...removedEditorEmbeds]));

                // Setup Endpoint (API)
                const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
                // Use API Endpoints for JSON response
                const endpoint = isEdit ? '/api/news/update/<?= $item['id'] ?? '' ?>' : '/api/news/store';
                // API expects POST for both (Store and Update)
                const method = 'POST';

                // UI Feedback
                const btn = document.getElementById('submitBtn');
                const btnText = document.getElementById('submitText');
                const originalText = btnText.textContent;

                btn.disabled = true;
                btnText.textContent = 'Menyimpan...';

                // Request
                fetch(endpoint, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(async response => {
                        const text = await response.text();
                        let data;

                        // Try to parse JSON
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Server Raw Response:', text);
                            throw new Error('Server returned invalid JSON. Cek Console untuk detail raw response.');
                        }

                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Server returned an error.');
                        }

                        return data;
                    })
                    .then(data => {
                        showNotification('success', data.message || 'Berita berhasil disimpan!');
                        setTimeout(() => window.location.href = '/admin/berita', 1000);
                    })
                    .catch(err => {
                        console.error('Fetch Error:', err);
                        showNotification('error', err.message || 'Terjadi kesalahan sistem');
                        btn.disabled = false;
                        btnText.textContent = originalText;
                    });
            });
        }

        // Helper: Notification
        function showNotification(type, message) {
            const bg = type === 'success' ? 'bg-green-600' : 'bg-red-600';
            const div = document.createElement('div');
            div.className = `fixed top-6 right-6 ${bg} text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-bounce flex items-center gap-3`;
            div.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3000);
        }
    });
</script>
