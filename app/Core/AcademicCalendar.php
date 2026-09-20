<?php
declare(strict_types=1);

final class AcademicCalendar
{
    public const MONTHS = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    public const CATEGORIES = ['kegiatan' => 'Kegiatan sekolah', 'ujian' => 'Ujian', 'libur' => 'Libur', 'akademik' => 'Akademik'];

    public static function currentYear(?DateTimeImmutable $now = null): int
    {
        $now ??= new DateTimeImmutable();
        return (int) $now->format('Y') - ((int) $now->format('n') < 7 ? 1 : 0);
    }

    public static function label(int $start): string { return $start . '/' . ($start + 1); }

    public static function months(int $start): array
    {
        $result = [];
        for ($offset = 0; $offset < 12; $offset++) {
            $date = (new DateTimeImmutable($start . '-07-01'))->modify("+{$offset} months");
            $result[$date->format('Y-m')] = self::MONTHS[(int) $date->format('n')] . ' ' . $date->format('Y');
        }
        return $result;
    }

    public static function validDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    public static function validateEvent(array $input, int $startYear): array
    {
        $data = [];
        foreach (['title', 'category', 'start_date', 'end_date', 'event_time', 'location', 'description', 'kind', 'status'] as $key) {
            if (isset($input[$key]) && !is_scalar($input[$key])) { throw new InvalidArgumentException('Format isian tidak valid.'); }
            $data[$key] = trim((string) ($input[$key] ?? ''));
        }
        if ($data['title'] === '' || mb_strlen($data['title']) > 180 || mb_strlen($data['location']) > 200 || mb_strlen($data['description']) > 20000) {
            throw new InvalidArgumentException('Judul wajib diisi (maksimal 180 karakter), lokasi maksimal 200 karakter, dan keterangan maksimal 20.000 karakter.');
        }
        if (!isset(self::CATEGORIES[$data['category']]) || !in_array($data['kind'], ['agenda', 'calendar'], true) || !in_array($data['status'], ['draft', 'published'], true)) {
            throw new InvalidArgumentException('Kategori, jenis, atau status tidak valid.');
        }
        if (!self::validDate($data['start_date']) || !self::validDate($data['end_date']) || $data['end_date'] < $data['start_date']) {
            throw new InvalidArgumentException('Tanggal selesai harus sama atau setelah tanggal mulai.');
        }
        if ($data['start_date'] < "$startYear-07-01" || $data['end_date'] > ($startYear + 1) . '-06-30') {
            throw new InvalidArgumentException('Tanggal kegiatan harus berada dalam Juli–Juni tahun pelajaran yang dipilih.');
        }
        if ($data['event_time'] !== '' && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $data['event_time'])) {
            throw new InvalidArgumentException('Waktu kegiatan tidak valid.');
        }
        $data['event_time'] = $data['event_time'] === '' ? null : $data['event_time'];
        $data['show_in_calendar'] = $data['kind'] === 'calendar' || ($input['show_in_calendar'] ?? '') === '1' ? 1 : 0;
        return $data;
    }
}
