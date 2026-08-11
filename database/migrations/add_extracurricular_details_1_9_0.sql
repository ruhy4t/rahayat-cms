-- Rahayat CMS 1.9.0
-- Idempotent MySQL/MariaDB migration. The legacy supervisor and schedule
-- columns are preserved so existing extracurricular data remains readable.

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ekstrakurikuler' AND column_name = 'supervisors_json') = 0,
    'ALTER TABLE ekstrakurikuler ADD COLUMN supervisors_json LONGTEXT NULL AFTER supervisor',
    'SELECT 1'
);
PREPARE extracurricular_migration FROM @sql;
EXECUTE extracurricular_migration;
DEALLOCATE PREPARE extracurricular_migration;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ekstrakurikuler' AND column_name = 'schedules_json') = 0,
    'ALTER TABLE ekstrakurikuler ADD COLUMN schedules_json LONGTEXT NULL AFTER supervisors_json',
    'SELECT 1'
);
PREPARE extracurricular_migration FROM @sql;
EXECUTE extracurricular_migration;
DEALLOCATE PREPARE extracurricular_migration;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ekstrakurikuler' AND column_name = 'achievements_json') = 0,
    'ALTER TABLE ekstrakurikuler ADD COLUMN achievements_json LONGTEXT NULL AFTER schedules_json',
    'SELECT 1'
);
PREPARE extracurricular_migration FROM @sql;
EXECUTE extracurricular_migration;
DEALLOCATE PREPARE extracurricular_migration;
