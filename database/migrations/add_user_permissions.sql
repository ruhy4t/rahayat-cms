-- ============================================
-- Migration: Add permissions JSON column to users
-- Run this on existing databases
-- ============================================

ALTER TABLE users
ADD COLUMN IF NOT EXISTS permissions JSON NULL COMMENT 'GTK configurable permissions (JSON array)'
AFTER is_spmb_committee;

-- Set default permissions for existing GTK users (all permissions)
UPDATE users
SET permissions = '["berita","kategori","galeri","slider","profil","fasilitas","staff","spmb"]'
WHERE role = 'gtk' AND permissions IS NULL;
