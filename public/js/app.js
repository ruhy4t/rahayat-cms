/**
 * ============================================
 * SchoolWeb CMS - Frontend JavaScript
 * SPA Navigation & Dynamic Content Loading
 * ============================================
 */

(function() {
    'use strict';

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ========================================
    // Content Protection
    // ========================================
    const ContentProtection = {
        isActive: false,
        protector: null,

        init() {
            this.protector = document.getElementById('contentProtectionOverlay');
            this.updateState(document);

            const showBlocker = () => {
                if (!this.protector) return;
                this.protector.classList.add('active');
                setTimeout(() => {
                    this.protector.classList.remove('active');
                }, 3000);
            };

            // 1. Block right-click
            document.addEventListener('contextmenu', (e) => {
                if (!this.isActive) return;
                e.preventDefault();
                showBlocker();
            });

            // 2. Block keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (!this.isActive) return;
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    try { navigator.clipboard.writeText(''); } catch(err) {}
                    showBlocker();
                    e.preventDefault();
                }
                if (e.ctrlKey || e.metaKey) {
                    const forbiddenKeys = ['c', 'p', 's', 'u', 'x', 'a'];
                    if (forbiddenKeys.includes(e.key.toLowerCase())) {
                        const tag = (e.target.tagName || '').toLowerCase();
                        if ((tag === 'input' || tag === 'textarea' || tag === 'select') && e.key.toLowerCase() === 'a') {
                            return;
                        }
                        showBlocker();
                        e.preventDefault();
                    }
                }
            });

            document.addEventListener('keyup', (e) => {
                if (!this.isActive) return;
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    try { navigator.clipboard.writeText(''); } catch(err) {}
                    showBlocker();
                }
            });

            // 3. Prevent image drag globally if active
            document.addEventListener('dragstart', (e) => {
                if (!this.isActive) return;
                if (e.target.tagName && e.target.tagName.toLowerCase() === 'img') {
                    e.preventDefault();
                }
            });

            // 4. Detect window blur (snipping tool)
            window.addEventListener('blur', () => {
                if (!this.isActive) return;
                if (this.protector) {
                    this.protector.style.display = 'flex';
                    this.protector.style.opacity = '1';
                }
            });

            window.addEventListener('focus', () => {
                if (!this.isActive) return;
                if (this.protector) {
                    this.protector.style.opacity = '0';
                    setTimeout(() => {
                        this.protector.style.display = 'none';
                    }, 300);
                }
            });
        },

        updateState(doc) {
            const meta = doc.querySelector('meta[name="content-protection"]');
            this.isActive = meta !== null && meta.content === 'true';
            
            if (this.isActive) {
                document.body.classList.add('content-protected');
            } else {
                document.body.classList.remove('content-protected');
            }
        }
    };

    // ========================================
    // SPA Navigation
    // ========================================
    const SPANavigator = {
        contentContainer: null,
        isLoading: false,

        init() {
            this.contentContainer = document.querySelector('main');
            if (!this.contentContainer) return;

            // Intercept link clicks for SPA navigation
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="/"]');
                if (link && !link.hasAttribute('target') && !link.hasAttribute('download')) {
                    const href = link.getAttribute('href');
                    // Skip admin and login routes
                    if (href.startsWith('/admin') || href === '/login' || href === '/logout') {
                        return;
                    }
                    e.preventDefault();
                    this.navigate(href);
                }
            });

            // Handle browser back/forward
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.url) {
                    this.loadContent(e.state.url, false);
                }
            });

            // Save initial state
            history.replaceState({ url: window.location.pathname }, '', window.location.pathname);
        },

        async navigate(url) {
            if (this.isLoading || url === window.location.pathname) return;
            
            // Update URL
            history.pushState({ url }, '', url);
            
            // Load content
            await this.loadContent(url, true);
        },

        async loadContent(url, showLoader = true) {
            if (this.isLoading) return;
            this.isLoading = true;

            // Show loading indicator
            if (showLoader) {
                this.showLoader();
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) throw new Error('Failed to load page');

                const html = await response.text();
                
                // Parse and extract main content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('main');
                const newTitle = doc.querySelector('title')?.textContent;

                if (newContent) {
                    // Fade out old content
                    this.contentContainer.style.opacity = '0';
                    
                    await this.sleep(150);
                    
                    // Replace content
                    this.contentContainer.innerHTML = newContent.innerHTML;
                    
                    // Update title
                    if (newTitle) {
                        document.title = newTitle;
                    }
                    
                    // Fade in new content
                    this.contentContainer.style.opacity = '1';
                    
                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Re-initialize lazy loading
                    this.initLazyLoading();

                    // Update content protection state based on the newly loaded page
                    ContentProtection.updateState(doc);
                }
            } catch (error) {
                console.error('Navigation error:', error);
                // Fallback to traditional navigation
                window.location.href = url;
            } finally {
                this.isLoading = false;
                this.hideLoader();
            }
        },

        showLoader() {
            const loader = document.createElement('div');
            loader.id = 'spa-loader';
            loader.innerHTML = `
                <div class="fixed top-0 left-0 right-0 h-1 bg-primary-100 z-50">
                    <div class="h-full bg-primary-600 animate-progress"></div>
                </div>
            `;
            document.body.appendChild(loader);
        },

        hideLoader() {
            const loader = document.getElementById('spa-loader');
            if (loader) loader.remove();
        },

        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        initLazyLoading() {
            const images = document.querySelectorAll('img[loading="lazy"]');
            if ('loading' in HTMLImageElement.prototype) {
                // Native lazy loading supported
                return;
            }
            
            // Fallback for older browsers
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => observer.observe(img));
        }
    };

    // ========================================
    // API Helper
    // ========================================
    const API = {
        async request(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };

            const mergedOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            };

            // If sending FormData, don't set Content-Type (browser will set it with boundary)
            if (options.body instanceof FormData) {
                delete mergedOptions.headers['Content-Type'];
            } else if (options.body && typeof options.body === 'object') {
                mergedOptions.headers['Content-Type'] = 'application/json';
                mergedOptions.body = JSON.stringify(options.body);
            }

            const response = await fetch(url, mergedOptions);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        },

        get(url) {
            return this.request(url, { method: 'GET' });
        },

        post(url, body) {
            return this.request(url, { method: 'POST', body });
        },

        delete(url) {
            return this.request(url, { method: 'POST', body: { _method: 'DELETE' } });
        }
    };

    // ========================================
    // Toast Notifications
    // ========================================
    const Toast = {
        show(message, type = 'success') {
            // Remove existing toasts
            document.querySelectorAll('.toast-notification').forEach(t => t.remove());

            const toast = document.createElement('div');
            toast.className = `toast-notification fixed bottom-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-full opacity-0 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-slate-800 text-white'
            }`;
            
            toast.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                </svg>
                <span class="font-medium">${message}</span>
            `;

            document.body.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-full', 'opacity-0');
            });

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        success(message) {
            this.show(message, 'success');
        },

        error(message) {
            this.show(message, 'error');
        }
    };

    // ========================================
    // Smooth Scroll
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // ========================================
    // Initialize
    // ========================================
    document.addEventListener('DOMContentLoaded', () => {
        ContentProtection.init();
        SPANavigator.init();
        SPANavigator.initLazyLoading();
    });

    // Expose to global scope
    window.API = API;
    window.Toast = Toast;
    window.SPANavigator = SPANavigator;
    window.ContentProtection = ContentProtection;

})();

// Add CSS for progress animation
const style = document.createElement('style');
style.textContent = `
    @keyframes progress {
        0% { width: 0%; }
        50% { width: 70%; }
        100% { width: 100%; }
    }
    .animate-progress {
        animation: progress 1s ease-in-out;
    }
    main {
        transition: opacity 0.15s ease-in-out;
    }
`;
document.head.appendChild(style);
