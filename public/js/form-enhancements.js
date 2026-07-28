/**
 * Make required fields unmistakable and accessible across public/admin forms.
 */
(function () {
    'use strict';

    function findFieldLabel(field) {
        const nativeLabel = field.labels?.[0] || field.closest('label');
        if (nativeLabel) return nativeLabel;

        let node = field;
        for (let depth = 0; depth < 3 && node?.parentElement; depth += 1) {
            if (node.previousElementSibling?.tagName === 'LABEL') {
                return node.previousElementSibling;
            }
            const directLabel = node.parentElement.querySelector(':scope > label');
            if (directLabel && !directLabel.contains(node)) {
                return directLabel;
            }
            node = node.parentElement;
            if (node.tagName === 'FORM') break;
        }
        return null;
    }

    function enhanceRequiredFields(root = document) {
        const forms = new Set(root.matches?.('form') ? [root] : root.querySelectorAll?.('form') || []);
        if (root.matches?.('input, select, textarea') && root.form) {
            forms.add(root.form);
        }

        forms.forEach((form) => {
            const requiredFields = [...form.querySelectorAll('input[required], select[required], textarea[required]')]
                .filter((field) => field.type !== 'hidden' && !field.disabled);

            form.querySelectorAll('.form-required-note, .required-badge, .optional-badge').forEach((item) => item.remove());

            requiredFields.forEach((field) => {
                const label = findFieldLabel(field);
                field.setAttribute('aria-required', 'true');

                if (
                    !label
                    || label.querySelector('.required-asterisk')
                    || label.textContent.includes('*')
                ) return;

                const asterisk = document.createElement('span');
                asterisk.className = 'required-asterisk';
                asterisk.textContent = '*';
                asterisk.setAttribute('aria-hidden', 'true');

                if (label.contains(field)) {
                    label.insertBefore(asterisk, field);
                } else {
                    label.appendChild(asterisk);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => enhanceRequiredFields(document));

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    enhanceRequiredFields(node);
                }
            });
        });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });

    window.enhanceRequiredFields = enhanceRequiredFields;
})();
