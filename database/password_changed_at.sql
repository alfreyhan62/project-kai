ALTER TABLE users ADD COLUMN password_changed_at DATETIME NULL AFTER password_hash;
UPDATE users SET password_changed_at = COALESCE(password_changed_at, created_at, NOW());
