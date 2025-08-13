-- PHP School Tuition Payment System (SPP) Database Setup
-- Create database and tables for the SPP system

-- Create database
CREATE DATABASE IF NOT EXISTS spp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spp_db;

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    class VARCHAR(20) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100),
    parent_name VARCHAR(100),
    parent_phone VARCHAR(15),
    address TEXT,
    monthly_fee DECIMAL(10,2) DEFAULT 500000.00,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_month VARCHAR(7) NOT NULL, -- Format: YYYY-MM
    status ENUM('Paid', 'Pending', 'Overdue') DEFAULT 'Pending',
    payment_method ENUM('Cash', 'Transfer', 'Online') DEFAULT 'Cash',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admin(id) ON DELETE SET NULL,
    UNIQUE KEY unique_student_month (student_id, payment_month)
);

-- Create payment_history table for tracking changes
CREATE TABLE IF NOT EXISTS payment_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    old_status ENUM('Paid', 'Pending', 'Overdue'),
    new_status ENUM('Paid', 'Pending', 'Overdue'),
    changed_by INT,
    change_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES admin(id) ON DELETE SET NULL
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin (username, password, full_name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@school.com');

-- Insert sample students
INSERT INTO students (student_id, name, class, phone, email, parent_name, parent_phone, address, monthly_fee) VALUES 
('2024001', 'Ahmad Rizki', 'X-A', '081234567890', 'ahmad@email.com', 'Budi Santoso', '081234567891', 'Jl. Merdeka No. 123', 500000.00),
('2024002', 'Siti Nurhaliza', 'X-B', '081234567892', 'siti@email.com', 'Andi Wijaya', '081234567893', 'Jl. Sudirman No. 456', 500000.00),
('2024003', 'Muhammad Fajar', 'XI-A', '081234567894', 'fajar@email.com', 'Dedi Kurniawan', '081234567895', 'Jl. Gatot Subroto No. 789', 550000.00);

-- Insert sample payments
INSERT INTO payments (student_id, amount, payment_date, payment_month, status, payment_method, created_by) VALUES 
(1, 500000.00, '2024-01-15', '2024-01', 'Paid', 'Transfer', 1),
(1, 500000.00, '2024-02-10', '2024-02', 'Paid', 'Cash', 1),
(2, 500000.00, '2024-01-20', '2024-01', 'Paid', 'Transfer', 1),
(3, 550000.00, '2024-01-25', '2024-01', 'Paid', 'Cash', 1);

-- Create indexes for better performance
CREATE INDEX idx_students_class ON students(class);
CREATE INDEX idx_students_status ON students(status);
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_payments_month ON payments(payment_month);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_date ON payments(payment_date);

-- Create view for payment summary
CREATE VIEW payment_summary AS
SELECT 
    s.id,
    s.student_id,
    s.name,
    s.class,
    s.monthly_fee,
    COUNT(p.id) as total_payments,
    SUM(CASE WHEN p.status = 'Paid' THEN p.amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN p.status = 'Pending' THEN p.amount ELSE 0 END) as total_pending,
    SUM(CASE WHEN p.status = 'Overdue' THEN p.amount ELSE 0 END) as total_overdue
FROM students s
LEFT JOIN payments p ON s.id = p.student_id
WHERE s.status = 'Active'
GROUP BY s.id, s.student_id, s.name, s.class, s.monthly_fee;
