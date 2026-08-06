<?php

declare(strict_types=1);

final class AlumniExcelExporter
{
    public static function download(array $items): never
    {
        $filename = 'data-alumni-' . date('Y-m-d') . '.xls';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        echo '<Styles>';
        echo '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="11"/></Style>';
        echo '<Style ss:ID="Title"><Font ss:Bold="1" ss:Size="16" ss:Color="#FFFFFF"/><Interior ss:Color="#1D4ED8" ss:Pattern="Solid"/><Alignment ss:Vertical="Center"/></Style>';
        echo '<Style ss:ID="Header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#334155" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/></Style>';
        echo '<Style ss:ID="Text"><Alignment ss:Vertical="Top" ss:WrapText="1"/></Style>';
        echo '<Style ss:ID="Number"><Alignment ss:Horizontal="Center" ss:Vertical="Top"/><NumberFormat ss:Format="0"/></Style>';
        echo '</Styles>';
        echo '<Worksheet ss:Name="Data Alumni"><Table>';

        $widths = [45, 180, 70, 85, 110, 80, 190, 120, 150, 170, 105, 170, 85, 70, 120];
        foreach ($widths as $width) {
            echo '<Column ss:AutoFitWidth="0" ss:Width="' . $width . '"/>';
        }

        echo '<Row ss:Height="28"><Cell ss:MergeAcross="14" ss:StyleID="Title"><Data ss:Type="String">Data Alumni ' . self::xml(SCHOOL_NAME) . '</Data></Cell></Row>';
        echo '<Row ss:Height="34">';
        foreach (['No.', 'Nama', 'Tahun Lulus', 'Kelas Terakhir', 'Melanjutkan Ke', 'Status Sekolah', 'Nama Sekolah Tujuan', 'Status Pekerjaan', 'Pekerjaan/Bidang', 'Instansi', 'Kota', 'Kontak Privat', 'Status Data', 'Inspiratif', 'Tanggal Masuk'] as $header) {
            echo self::cell($header, 'String', 'Header');
        }
        echo '</Row>';

        $statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        foreach ($items as $index => $item) {
            [$continuationType, $continuationStatus, $continuationInstitution] = self::continuationParts($item);
            echo '<Row>';
            echo self::cell((string) ($index + 1), 'Number', 'Number');
            echo self::cell((string) ($item['name'] ?? ''));
            echo self::cell((string) ((int) ($item['graduation_year'] ?? 0)), 'Number', 'Number');
            echo self::cell((string) ($item['final_class'] ?? ''));
            echo self::cell($continuationType);
            echo self::cell($continuationStatus);
            echo self::cell($continuationInstitution);
            echo self::cell((string) ($item['employment_status'] ?? ''));
            echo self::cell((string) ($item['occupation'] ?? ''));
            echo self::cell((string) ($item['institution'] ?? ''));
            echo self::cell((string) ($item['city'] ?? ''));
            echo self::cell((string) ($item['contact_plain'] ?? ''));
            echo self::cell($statusLabels[$item['status'] ?? ''] ?? (string) ($item['status'] ?? ''));
            echo self::cell(!empty($item['is_featured']) ? 'Ya' : 'Tidak');
            echo self::cell((string) ($item['created_at'] ?? ''));
            echo '</Row>';
        }

        $lastRow = max(2, count($items) + 2);
        echo '</Table><WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">'
            . '<FreezePanes/><FrozenNoSplit/><SplitHorizontal>2</SplitHorizontal><TopRowBottomPane>2</TopRowBottomPane>'
            . '<ProtectObjects>False</ProtectObjects><ProtectScenarios>False</ProtectScenarios>'
            . '</WorksheetOptions>';
        echo '<AutoFilter x:Range="R2C1:R' . $lastRow . 'C15" xmlns="urn:schemas-microsoft-com:office:excel"/>';
        echo '</Worksheet></Workbook>';
        exit;
    }

    private static function continuationParts(array $item): array
    {
        $type = trim((string) ($item['continuation_type'] ?? ''));
        $status = trim((string) ($item['continuation_status'] ?? ''));
        $institution = trim((string) ($item['continuation_institution'] ?? ''));

        if ($type === '' && !empty($item['further_education'])) {
            $parts = array_map('trim', explode(' — ', (string) $item['further_education']));
            $type = (string) array_shift($parts);
            if (in_array($parts[0] ?? '', ['Negeri', 'Swasta'], true)) {
                $status = (string) array_shift($parts);
            }
            $institution = implode(' — ', $parts);
        }

        return [$type, $status, $institution];
    }

    private static function cell(string $value, string $type = 'String', string $style = 'Text'): string
    {
        return '<Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $type . '">'
            . self::xml($value) . '</Data></Cell>';
    }

    private static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
