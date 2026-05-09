-- Migration to add per-day operating hours columns
-- Adds is_closed flags for Mon-Sat and separate sun open/close fields

ALTER TABLE school_profile 
    ADD COLUMN is_closed_monday TINYINT(1) DEFAULT 0 AFTER monday_close,
    ADD COLUMN is_closed_tuesday TINYINT(1) DEFAULT 0 AFTER tuesday_close,
    ADD COLUMN is_closed_wednesday TINYINT(1) DEFAULT 0 AFTER wednesday_close,
    ADD COLUMN is_closed_thursday TINYINT(1) DEFAULT 0 AFTER thursday_close,
    ADD COLUMN is_closed_friday TINYINT(1) DEFAULT 0 AFTER friday_close,
    ADD COLUMN is_closed_saturday TINYINT(1) DEFAULT 0 AFTER saturday_close;

-- For sunday we need to add open/close times (is_closed_sunday already exists)
ALTER TABLE school_profile
    ADD COLUMN sunday_open VARCHAR(5) DEFAULT '07:00' AFTER is_closed_sunday,
    ADD COLUMN sunday_close VARCHAR(5) DEFAULT '15:00' AFTER sunday_open;
