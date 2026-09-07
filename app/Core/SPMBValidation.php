<?php

declare(strict_types=1);

final class SPMBValidation
{
    public static function errors(array $data): array
    {
        $errors = [];
        foreach (['student_name', 'birth_place', 'birth_date', 'address', 'address_village',
            'address_district', 'address_city', 'address_province'] as $field) {
            if (trim((string) ($data[$field] ?? '')) === '') { $errors[$field] = 'Kolom ini wajib diisi.'; }
        }
        foreach (['nisn' => 10, 'nik' => 16, 'previous_school_npsn' => 8] as $field => $length) {
            if (!preg_match('/^\d{' . $length . '}$/D', (string) ($data[$field] ?? ''))) {
                $errors[$field] = "Harus terdiri dari {$length} digit angka.";
            }
        }
        if (!in_array($data['gender'] ?? '', ['L', 'P'], true)) { $errors['gender'] = 'Pilih jenis kelamin yang valid.'; }
        $date = (string) ($data['birth_date'] ?? '');
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date || $date > date('Y-m-d') || $date < '1900-01-01') {
            $errors['birth_date'] = 'Tanggal lahir tidak valid.';
        }
        foreach ($data as $field => $value) {
            $max = match ($field) {
                'address', 'previous_school_address' => 2000,
                'previous_school' => 255, 'religion' => 50,
                'phone', 'father_phone', 'mother_phone' => 20,
                default => 100,
            };
            if (is_array($value) || mb_strlen((string) $value) > $max) { $errors[$field] = "Maksimal {$max} karakter."; }
        }
        foreach (['phone', 'father_phone', 'mother_phone'] as $field) {
            if (!empty($data[$field]) && !preg_match('/^\+?[0-9 ()-]{6,20}$/D', $data[$field])) {
                $errors[$field] = 'Nomor telepon tidak valid.';
            }
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Alamat email tidak valid.'; }
        $year = $data['graduation_year'] ?? '';
        if ($year !== '' && $year !== null && (!ctype_digit((string) $year) || (int) $year < 1901 || (int) $year > (int) date('Y') + 1)) {
            $errors['graduation_year'] = 'Tahun kelulusan tidak valid.';
        }
        return $errors;
    }
}
