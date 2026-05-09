-- ============================================
-- Migration: Add watermark_enabled to school_profile
-- Run this on existing databases
-- ============================================

ALTER TABLE school_profile
ADD COLUMN IF NOT EXISTS watermark_enabled TINYINT(1) DEFAULT 1 COMMENT 'Enable watermark on uploaded images'
AFTER graduation_rate;
