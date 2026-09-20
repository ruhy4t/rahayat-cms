<?php
declare(strict_types=1);

class SchoolEvent extends Model
{
    protected string $table = 'school_events';
    protected array $fillable = ['academic_year_id', 'kind', 'title', 'category', 'start_date', 'end_date', 'event_time', 'location', 'description', 'show_in_calendar', 'status'];

    public function years(bool $public = true): array
    {
        return $this->db->fetchAll('SELECT * FROM academic_years' . ($public ? ' WHERE is_published = 1' : '') . ' ORDER BY start_year DESC');
    }

    public function year(int $id): array|false { return $this->db->fetch('SELECT * FROM academic_years WHERE id = ?', [$id]); }

    public function saveYear(int $start, bool $published, ?string $pdf, bool $replacePdf): void
    {
        $this->db->query('INSERT INTO academic_years (start_year, is_published) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_published = VALUES(is_published)', [$start, (int) $published]);
        if ($replacePdf) { $this->db->query('UPDATE academic_years SET pdf_path = ? WHERE start_year = ?', [$pdf, $start]); }
    }

    public function publicAgenda(bool $archive = false, int $page = 1, int $limit = 12): array
    {
        $limit = min(30, max(1, $limit));
        $offset = (min(10000, max(1, $page)) - 1) * $limit;
        $comparison = $archive ? '<' : '>=';
        $direction = $archive ? 'DESC' : 'ASC';
        return $this->db->fetchAll("SELECT e.* FROM school_events e JOIN academic_years y ON y.id = e.academic_year_id
            WHERE e.status = 'published' AND y.is_published = 1 AND e.kind = 'agenda' AND e.end_date $comparison ?
            ORDER BY e.start_date $direction, e.id $direction LIMIT $limit OFFSET $offset", [date('Y-m-d')]);
    }

    public function publishedEvent(int $id): array|false
    {
        return $this->db->fetch("SELECT e.*, y.start_year FROM school_events e JOIN academic_years y ON y.id = e.academic_year_id
            WHERE e.id = ? AND e.status = 'published' AND y.is_published = 1", [$id]);
    }

    public function calendar(int $yearId, string $month): array
    {
        $first = $month . '-01';
        $last = (new DateTimeImmutable($first))->format('Y-m-t');
        return $this->db->fetchAll("SELECT e.* FROM school_events e JOIN academic_years y ON y.id = e.academic_year_id
            WHERE e.academic_year_id = ? AND y.is_published = 1 AND e.status = 'published'
            AND (e.kind = 'calendar' OR e.show_in_calendar = 1) AND e.start_date <= ? AND e.end_date >= ?
            ORDER BY e.start_date, e.id", [$yearId, $last, $first]);
    }

    public function adminEvents(string $kind, int $yearId, int $page): array
    {
        $offset = (min(10000, max(1, $page)) - 1) * 30;
        return $this->db->fetchAll("SELECT e.*, y.start_year FROM school_events e JOIN academic_years y ON y.id = e.academic_year_id
            WHERE e.kind = ? AND e.academic_year_id = ? ORDER BY e.start_date DESC, e.id DESC LIMIT 30 OFFSET $offset", [$kind, $yearId]);
    }

    public function calendarData(?int $requestedYear = null, ?string $requestedMonth = null): array
    {
        $years = $this->years();
        $selected = null;
        $target = $requestedYear ?? AcademicCalendar::currentYear();
        foreach ($years as $year) { if ((int) $year['start_year'] === $target) { $selected = $year; break; } }
        // Never show an old calendar as if it belonged to the current academic year.
        if ($selected === null) { return ['years' => $years, 'year' => null, 'months' => [], 'month' => null, 'events' => []]; }
        $months = AcademicCalendar::months((int) $selected['start_year']);
        $month = $requestedMonth ?? date('Y-m');
        if (!isset($months[$month])) { $month = array_key_first($months); }
        return ['years' => $years, 'year' => $selected, 'months' => $months, 'month' => $month, 'events' => $this->calendar((int) $selected['id'], $month)];
    }
}
