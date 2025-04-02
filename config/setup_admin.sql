-- User roles table
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Add user role column to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS role_id INT NULL;

-- Create admin activity log table if not exists
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default roles if not exists
INSERT INTO user_roles (name, description) 
SELECT * FROM (
    SELECT 'super_admin' as name, 'Full access to all system features including data modification and deletion' as description
) AS tmp
WHERE NOT EXISTS (
    SELECT name FROM user_roles WHERE name = 'super_admin'
) LIMIT 1;

INSERT INTO user_roles (name, description) 
SELECT * FROM (
    SELECT 'admin' as name, 'Access to daily management functions without ability to modify critical data' as description
) AS tmp
WHERE NOT EXISTS (
    SELECT name FROM user_roles WHERE name = 'admin'
) LIMIT 1;

INSERT INTO user_roles (name, description) 
SELECT * FROM (
    SELECT 'accountant' as name, 'Access to financial records and ability to export sales data' as description
) AS tmp
WHERE NOT EXISTS (
    SELECT name FROM user_roles WHERE name = 'accountant'
) LIMIT 1;

INSERT INTO user_roles (name, description) 
SELECT * FROM (
    SELECT 'agent' as name, 'Sales agent with customer management and commission tracking' as description
) AS tmp
WHERE NOT EXISTS (
    SELECT name FROM user_roles WHERE name = 'agent'
) LIMIT 1;

INSERT INTO user_roles (name, description) 
SELECT * FROM (
    SELECT 'customer' as name, 'Regular user with access to personal dashboard and claim management' as description
) AS tmp
WHERE NOT EXISTS (
    SELECT name FROM user_roles WHERE name = 'customer'
) LIMIT 1;

-- Set user ID 1 as super_admin
UPDATE users SET role_id = (SELECT id FROM user_roles WHERE name = 'super_admin') WHERE id = 1; 