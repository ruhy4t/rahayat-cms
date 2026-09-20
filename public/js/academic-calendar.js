(() => {
    'use strict';
    document.querySelectorAll('[data-academic-calendar]').forEach(calendar => {
        const heading = calendar.querySelector('[data-calendar-heading]');
        if (!heading) return;
        const empty = calendar.querySelector('[data-calendar-empty]');
        const reset = calendar.querySelector('[data-calendar-reset]');
        const events = [...calendar.querySelectorAll('[data-event-start]')];
        const buttons = [...calendar.querySelectorAll('[data-calendar-date]')];
        function select(date) {
            let count = 0;
            events.forEach(event => {
                const visible = !date || (event.dataset.eventStart <= date && event.dataset.eventEnd >= date);
                event.hidden = !visible;
                if (visible) count++;
            });
            buttons.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.calendarDate === date)));
            heading.textContent = date ? new Intl.DateTimeFormat('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}).format(new Date(date + 'T12:00:00')) : 'Kegiatan bulan ini';
            empty.hidden = count > 0;
            empty.textContent = date ? 'Tidak ada kegiatan pada tanggal ini.' : 'Belum ada kegiatan pada bulan ini.';
            reset.hidden = !date;
        }
        buttons.forEach(button => button.addEventListener('click', () => select(button.dataset.calendarDate)));
        reset.addEventListener('click', () => select(null));
    });
})();
