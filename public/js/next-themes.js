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
});
