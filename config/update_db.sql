-- Add remember_token field to users table
ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password;

-- Add google_id field to users table for Google OAuth
ALTER TABLE users ADD COLUMN google_id VARCHAR(100) NULL AFTER email; 