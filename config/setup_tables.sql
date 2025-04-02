-- Users table (already exists, adding foreign key reference)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    google_id VARCHAR(100) NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255) NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    postcode VARCHAR(20) NULL,
    state VARCHAR(100) NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year VARCHAR(4) NOT NULL,
    reg_number VARCHAR(20) NOT NULL,
    engine_no VARCHAR(50) NULL,
    chassis_no VARCHAR(50) NULL,
    color VARCHAR(30) NULL,
    mileage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, reg_number)
);

-- Protection plans table
CREATE TABLE IF NOT EXISTS protection_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    plan_id INT NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    coverage_details TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    payment_status ENUM('paid', 'pending', 'failed') NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Workshops table
CREATE TABLE IF NOT EXISTS workshops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postcode VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Claims table
CREATE TABLE IF NOT EXISTS claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    workshop_id INT NOT NULL,
    issue_type VARCHAR(50) NOT NULL,
    issue_description TEXT NOT NULL,
    mileage INT NOT NULL,
    issue_date DATE NOT NULL,
    amount DECIMAL(10, 2) NULL,
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (workshop_id) REFERENCES workshops(id)
);

-- Claim timeline table
CREATE TABLE IF NOT EXISTS claim_timeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    claim_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    event_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT NOT NULL,
    FOREIGN KEY (claim_id) REFERENCES claims(id) ON DELETE CASCADE
);

-- Claim notes table
CREATE TABLE IF NOT EXISTS claim_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    claim_id INT NOT NULL,
    user_id INT NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (claim_id) REFERENCES claims(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Claim photos table
CREATE TABLE IF NOT EXISTS claim_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    claim_id INT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (claim_id) REFERENCES claims(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    reference_no VARCHAR(50) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'active') NOT NULL DEFAULT 'pending',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES protection_plans(id) ON DELETE CASCADE
);

-- Support requests table
CREATE TABLE IF NOT EXISTS support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Support replies table
CREATE TABLE IF NOT EXISTS support_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES support_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Support attachments table
CREATE TABLE IF NOT EXISTS support_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    reply_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES support_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_id) REFERENCES support_replies(id) ON DELETE CASCADE
);

-- Support categories table
CREATE TABLE IF NOT EXISTS support_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default support categories
INSERT INTO support_categories (name, description) VALUES
('Account Issues', 'Questions about account management, login, and profile settings'),
('Billing & Payments', 'Issues related to billing, payments, and subscription management'),
('Protection Plans', 'Questions about plan coverage, benefits, and policy details'),
('Claims', 'Support for submitting and tracking claims'),
('Technical Support', 'Help with technical issues and system problems'),
('Other', 'General inquiries and other support needs');

-- Insert sample workshops
INSERT INTO workshops (name, address, city, state, postcode, phone, email)
VALUES 
('AutoCare Services', '123 Jalan Ampang', 'Kuala Lumpur', 'Wilayah Persekutuan', '50450', '03-1234 5678', 'service@autocare.my'),
('Toyota Service Center', '456 Jalan PJ', 'Petaling Jaya', 'Selangor', '47800', '03-8765 4321', 'service@toyota.my'),
('Electron Auto Repair', '789 Jalan Kepong', 'Kuala Lumpur', 'Wilayah Persekutuan', '52100', '03-9876 5432', 'service@electron.my'),
('PPA Partner Workshop', '101 Jalan Cheras', 'Kuala Lumpur', 'Wilayah Persekutuan', '56100', '03-2468 1357', 'workshop@ppa.my'); 