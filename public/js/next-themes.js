document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.next-header');
    if (!header) return;
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        const active = document.activeElement;
        const branch = active?.closest('.next-mobile-nav') || active?.closest('details');
        header.querySelectorAll('details[open]').forEach(detail => { detail.open = false; });
        if (branch) branch.querySelector('summary')?.focus();
    });
    document.addEventListener('click', event => {
        if (!header.contains(event.target)) header.querySelectorAll('details[open]').forEach(detail => { detail.open = false; });
    });
    header.querySelectorAll('.next-desktop-nav>.next-menu-list>li details').forEach(detail => {
        detail.addEventListener('toggle', () => {
            if (!detail.open) return;
            header.querySelectorAll('.next-desktop-nav>.next-menu-list>li details[open]').forEach(other => {
                if (other !== detail && !other.contains(detail) && !detail.contains(other)) other.open = false;
            });
        });
    });
    const desktopPointer = window.matchMedia('(min-width: 1101px) and (hover: hover) and (pointer: fine)');
    header.querySelectorAll('.next-desktop-nav .next-menu-parent').forEach(parent => {
        const branch = parent.querySelector(':scope > details');
        if (!branch) return;
        parent.addEventListener('pointerenter', event => {
            if (desktopPointer.matches && event.pointerType !== 'touch') branch.open = true;
        });
        parent.addEventListener('pointerleave', () => {
            if (desktopPointer.matches && !parent.contains(document.activeElement)) branch.open = false;
        });
        parent.addEventListener('focusin', () => { branch.open = true; });
        parent.addEventListener('focusout', event => {
            if (!parent.contains(event.relatedTarget)) branch.open = false;
        });
    });
    const form = document.querySelector('form[action="/alumni/kirim"]');
    const continuation = form?.querySelector('[name="further_education"]');
    const updateContinuation = () => {
        const required = !['Bekerja', 'Tidak Melanjutkan'].includes(continuation.value);
        for (const name of ['further_education_status', 'further_education_detail']) {
            const field = form.querySelector(`[name="${name}"]`);
            field.required = required;
            field.disabled = !required;
            field.closest('label').hidden = !required;
        }
    };
    if (continuation) { continuation.addEventListener('change', updateContinuation); updateContinuation(); }

    const viewer = document.querySelector('[data-gallery-viewer]');
    if (viewer && typeof viewer.showModal === 'function') {
        const pictures = [...document.querySelectorAll('[data-gallery-image]')];
        const image = viewer.querySelector('img');
        const caption = viewer.querySelector('[data-gallery-caption]');
        const previous = viewer.querySelector('[data-gallery-previous]');
        const next = viewer.querySelector('[data-gallery-next]');
        let index = 0;
        const show = position => {
            index = (position + pictures.length) % pictures.length;
            image.src = pictures[index].href;
            image.alt = pictures[index].dataset.caption || '';
            caption.textContent = image.alt;
            previous.hidden = next.hidden = pictures.length < 2;
        };
        pictures.forEach((picture, position) => picture.addEventListener('click', event => {
            event.preventDefault();
            show(position);
            viewer.showModal();
        }));
        viewer.querySelector('[data-gallery-close]').addEventListener('click', () => viewer.close());
        previous.addEventListener('click', () => show(index - 1));
        next.addEventListener('click', () => show(index + 1));
        viewer.addEventListener('keydown', event => {
            if (event.key === 'ArrowLeft') { event.preventDefault(); show(index - 1); }
            if (event.key === 'ArrowRight') { event.preventDefault(); show(index + 1); }
        });
        viewer.addEventListener('click', event => { if (event.target === viewer) viewer.close(); });
    }
});
