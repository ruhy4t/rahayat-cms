-- Rahayat CMS 1.7.0 - Analitik dan data lanjutan alumni
-- Idempoten: aman dijalankan berulang kali melalui phpMyAdmin.
-- Tidak menghapus kolom atau data alumni yang sudah ada.

SET @column_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'alumni' AND column_name = 'continuation_type'
);
SET @migration_sql = IF(
    @column_exists = 0,
    'ALTER TABLE `alumni` ADD COLUMN `continuation_type` VARCHAR(40) NULL AFTER `further_education`',
    'DO 0'
);
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'alumni' AND column_name = 'continuation_status'
);
SET @migration_sql = IF(
    @column_exists = 0,
    'ALTER TABLE `alumni` ADD COLUMN `continuation_status` VARCHAR(20) NULL AFTER `continuation_type`',
    'DO 0'
);
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'alumni' AND column_name = 'continuation_institution'
);
SET @migration_sql = IF(
    @column_exists = 0,
    'ALTER TABLE `alumni` ADD COLUMN `continuation_institution` VARCHAR(120) NULL AFTER `continuation_status`',
    'DO 0'
);
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'alumni' AND column_name = 'employment_status'
);
SET @migration_sql = IF(
    @column_exists = 0,
    'ALTER TABLE `alumni` ADD COLUMN `employment_status` VARCHAR(40) NULL AFTER `continuation_institution`',
    'DO 0'
);
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

-- Isi kolom terstruktur hanya jika masih kosong. Nilai lama tetap dipertahankan.
UPDATE alumni
SET continuation_type = CASE
    WHEN TRIM(SUBSTRING_INDEX(further_education, ' — ', 1)) = 'Tidak/Belum Melanjutkan' THEN 'Tidak Melanjutkan'
    ELSE NULLIF(TRIM(SUBSTRING_INDEX(further_education, ' — ', 1)), '')
END
WHERE continuation_type IS NULL AND further_education IS NOT NULL AND further_education <> '';

UPDATE alumni
SET continuation_status = SUBSTRING_INDEX(SUBSTRING_INDEX(further_education, ' — ', 2), ' — ', -1)
WHERE continuation_status IS NULL
  AND SUBSTRING_INDEX(SUBSTRING_INDEX(further_education, ' — ', 2), ' — ', -1) IN ('Negeri', 'Swasta');

UPDATE alumni
SET continuation_institution = CASE
    WHEN continuation_status IN ('Negeri', 'Swasta')
        THEN NULLIF(TRIM(SUBSTRING(further_education, CHAR_LENGTH(SUBSTRING_INDEX(further_education, ' — ', 2)) + 4)), '')
    ELSE NULLIF(TRIM(SUBSTRING(further_education, CHAR_LENGTH(SUBSTRING_INDEX(further_education, ' — ', 1)) + 4)), '')
END
WHERE continuation_institution IS NULL AND further_education LIKE '% — %';

-- Samakan diksi lama tanpa menyentuh pilihan alumni lainnya.
UPDATE alumni
SET continuation_type = 'Tidak Melanjutkan'
WHERE continuation_type = 'Tidak/Belum Melanjutkan'
   OR further_education = 'Tidak/Belum Melanjutkan';
