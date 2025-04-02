-- Add google_id column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS google_id VARCHAR(100) NULL AFTER remember_token; 