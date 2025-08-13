<?php
/**
 * Common utility functions for PHP School Tuition Payment System (SPP)
 * Contains helper functions used throughout the application
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

/**
 * Authentication Functions
 */

/**
 * Authenticate admin user
 */
function authenticateAdmin($username, $password) {
    try {
        $sql = "SELECT id, username, password, full_name, email FROM admin WHERE username = ? AND id > 0";
        $admin = fetchSingle($sql, [$username]);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['last_activity'] = time();
            
            // Log login activity
            logActivity($admin['id'], 'login', 'Admin logged in');
            
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Log admin activity
 */
function logActivity($adminId, $action, $description) {
    try {
        $sql = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        executeQuery($sql, [$adminId, $action, $description, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        error_log("Activity logging error: " . $e->getMessage());
    }
}

/**
 * Student Management Functions
 */

/**
 * Get all students with optional search and pagination
 */
function getStudents($search = '', $page = 1, $perPage = RECORDS_PER_PAGE) {
    try {
        $sql = "SELECT * FROM students WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $searchConditions = buildSearchConditions(['name', 'student_id', 'class', 'parent_name'], $search);
            if (!empty($searchConditions[0])) {
                $sql .= " AND " . $searchConditions[0];
                $params = array_merge($params, $searchConditions[1]);
            }
        }
        
        $sql .= " ORDER BY name ASC";
        
        return getPaginatedResults($sql, $params, $page, $perPage);
    } catch (Exception $e) {
        error_log("Get students error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get student by ID
 */
function getStudentById($id) {
    try {
        $sql = "SELECT * FROM students WHERE id = ?";
        return fetchSingle($sql, [$id]);
    } catch (Exception $e) {
        error_log("Get student by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get student by student ID
 */
function getStudentByStudentId($studentId) {
    try {
        $sql = "SELECT * FROM students WHERE student_id = ?";
        return fetchSingle($sql, [$studentId]);
    } catch (Exception $e) {
        error_log("Get student by student ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Add new student
 */
function addStudent($data) {
    try {
        // Check if student ID already exists
        if (getStudentByStudentId($data['student_id'])) {
            throw new Exception("Student ID already exists");
        }
        
        $sql = "INSERT INTO students (student_id, name, class, phone, email, parent_name, parent_phone, address, monthly_fee, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['student_id'],
            $data['name'],
            $data['class'],
            $data['phone'],
            $data['email'],
            $data['parent_name'],
            $data['parent_phone'],
            $data['address'],
            $data['monthly_fee'],
            $data['status'] ?? 'Active'
        ];
        
        $studentId = insertData($sql, $params);
        
        // Log activity
        if (isset($_SESSION['admin_id'])) {
            logActivity($_SESSION['admin_id'], 'add_student', "Added student: {$data['name']} ({$data['student_id']})");
        }
        
        return $studentId;
    } catch (Exception $e) {
        error_log("Add student error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Update student
 */
function updateStudent($id, $data) {
    try {
        // Check if student exists
        $existingStudent = getStudentById($id);
        if (!$existingStudent) {
            throw new Exception("Student not found");
        }
        
        // Check if student ID already exists for other students
        $sql = "SELECT id FROM students WHERE student_id = ? AND id != ?";
        $duplicate = fetchSingle($sql, [$data['student_id'], $id]);
        if ($duplicate) {
            throw new Exception("Student ID already exists");
        }
        
        $sql = "UPDATE students SET student_id = ?, name = ?, class = ?, phone = ?, email = ?, 
                parent_name = ?, parent_phone = ?, address = ?, monthly_fee = ?, status = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['student_id'],
            $data['name'],
            $data['class'],
            $data['phone'],
            $data['email'],
            $data['parent_name'],
            $data['parent_phone'],
            $data['address'],
            $data['monthly_fee'],
            $data['status'],
            $id
        ];
        
        $affected = updateData($sql, $params);
        
        // Log activity
        if (isset($_SESSION['admin_id'])) {
            logActivity($_SESSION['admin_id'], 'update_student', "Updated student: {$data['name']} ({$data['student_id']})");
        }
        
        return $affected > 0;
    } catch (Exception $e) {
        error_log("Update student error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Delete student
 */
function deleteStudent($id) {
    try {
        $student = getStudentById($id);
        if (!$student) {
            throw new Exception("Student not found");
        }
        
        // Check if student has payments
        $paymentCount = getCount('payments', ['student_id = ?'], [$id]);
        if ($paymentCount > 0) {
            throw new Exception("Cannot delete student with existing payments");
        }
        
        $sql = "DELETE FROM students WHERE id = ?";
        $affected = updateData($sql, [$id]);
        
        // Log activity
        if (isset($_SESSION['admin_id'])) {
            logActivity($_SESSION['admin_id'], 'delete_student', "Deleted student: {$student['name']} ({$student['student_id']})");
        }
        
        return $affected > 0;
    } catch (Exception $e) {
        error_log("Delete student error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Payment Management Functions
 */

/**
 * Get payments with student information
 */
function getPayments($search = '', $status = '', $month = '', $page = 1, $perPage = RECORDS_PER_PAGE) {
    try {
        $sql = "SELECT p.*, s.name as student_name, s.student_id, s.class 
                FROM payments p 
                JOIN students s ON p.student_id = s.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (s.name LIKE ? OR s.student_id LIKE ? OR s.class LIKE ?)";
            $searchTerm = '%' . escapeLike($search) . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($status)) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
        
        if (!empty($month)) {
            $sql .= " AND p.payment_month = ?";
            $params[] = $month;
        }
        
        $sql .= " ORDER BY p.payment_date DESC, s.name ASC";
        
        return getPaginatedResults($sql, $params, $page, $perPage);
    } catch (Exception $e) {
        error_log("Get payments error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get payment by ID
 */
function getPaymentById($id) {
    try {
        $sql = "SELECT p.*, s.name as student_name, s.student_id, s.class 
                FROM payments p 
                JOIN students s ON p.student_id = s.id 
                WHERE p.id = ?";
        return fetchSingle($sql, [$id]);
    } catch (Exception $e) {
        error_log("Get payment by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Add new payment
 */
function addPayment($data) {
    try {
        // Check if payment for this student and month already exists
        $sql = "SELECT id FROM payments WHERE student_id = ? AND payment_month = ?";
        $existing = fetchSingle($sql, [$data['student_id'], $data['payment_month']]);
        if ($existing) {
            throw new Exception("Payment for this month already exists");
        }
        
        $sql = "INSERT INTO payments (student_id, amount, payment_date, payment_month, status, payment_method, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['student_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_month'],
            $data['status'],
            $data['payment_method'],
            $data['notes'],
            $_SESSION['admin_id'] ?? null
        ];
        
        $paymentId = insertData($sql, $params);
        
        // Log activity
        if (isset($_SESSION['admin_id'])) {
            $student = getStudentById($data['student_id']);
            logActivity($_SESSION['admin_id'], 'add_payment', "Added payment for {$student['name']} - {$data['payment_month']}");
        }
        
        return $paymentId;
    } catch (Exception $e) {
        error_log("Add payment error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Update payment
 */
function updatePayment($id, $data) {
    try {
        $existingPayment = getPaymentById($id);
        if (!$existingPayment) {
            throw new Exception("Payment not found");
        }
        
        $sql = "UPDATE payments SET amount = ?, payment_date = ?, status = ?, payment_method = ?, notes = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['amount'],
            $data['payment_date'],
            $data['status'],
            $data['payment_method'],
            $data['notes'],
            $id
        ];
        
        $affected = updateData($sql, $params);
        
        // Log activity
        if (isset($_SESSION['admin_id'])) {
            logActivity($_SESSION['admin_id'], 'update_payment', "Updated payment ID: $id");
        }
        
        return $affected > 0;
    } catch (Exception $e) {
        error_log("Update payment error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    try {
        $stats = [];
        
        // Total students
        $stats['total_students'] = getCount('students', ['status = ?'], ['Active']);
        
        // Total payments this month
        $currentMonth = getCurrentMonth();
        $stats['payments_this_month'] = getCount('payments', ['payment_month = ?'], [$currentMonth]);
        
        // Total revenue this month
        $sql = "SELECT SUM(amount) as total FROM payments WHERE payment_month = ? AND status = 'Paid'";
        $result = fetchSingle($sql, [$currentMonth]);
        $stats['revenue_this_month'] = $result['total'] ?? 0;
        
        // Pending payments
        $stats['pending_payments'] = getCount('payments', ['status = ?'], ['Pending']);
        
        // Overdue payments
        $stats['overdue_payments'] = getCount('payments', ['status = ?'], ['Overdue']);
        
        return $stats;
    } catch (Exception $e) {
        error_log("Get dashboard stats error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent activities
 */
function getRecentActivities($limit = 10) {
    try {
        $sql = "SELECT al.*, a.full_name as admin_name 
                FROM admin_logs al 
                JOIN admin a ON al.admin_id = a.id 
                ORDER BY al.created_at DESC 
                LIMIT ?";
        return fetchAll($sql, [$limit]);
    } catch (Exception $e) {
        error_log("Get recent activities error: " . $e->getMessage());
        return [];
    }
}

/**
 * Validation Functions
 */

/**
 * Validate student data
 */
function validateStudentData($data, $isUpdate = false) {
    $errors = [];
    
    if (empty($data['student_id'])) {
        $errors[] = "Student ID is required";
    }
    
    if (empty($data['name'])) {
        $errors[] = "Student name is required";
    }
    
    if (empty($data['class'])) {
        $errors[] = "Class is required";
    }
    
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = "Invalid email format";
    }
    
    if (!empty($data['phone']) && !isValidPhone($data['phone'])) {
        $errors[] = "Invalid phone number format";
    }
    
    if (!empty($data['parent_phone']) && !isValidPhone($data['parent_phone'])) {
        $errors[] = "Invalid parent phone number format";
    }
    
    if (empty($data['monthly_fee']) || !is_numeric($data['monthly_fee']) || $data['monthly_fee'] <= 0) {
        $errors[] = "Valid monthly fee is required";
    }
    
    return $errors;
}

/**
 * Validate payment data
 */
function validatePaymentData($data) {
    $errors = [];
    
    if (empty($data['student_id'])) {
        $errors[] = "Student is required";
    }
    
    if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
        $errors[] = "Valid payment amount is required";
    }
    
    if (empty($data['payment_date'])) {
        $errors[] = "Payment date is required";
    }
    
    if (empty($data['payment_month'])) {
        $errors[] = "Payment month is required";
    }
    
    if (empty($data['status']) || !in_array($data['status'], ['Paid', 'Pending', 'Overdue'])) {
        $errors[] = "Valid payment status is required";
    }
    
    if (empty($data['payment_method']) || !in_array($data['payment_method'], ['Cash', 'Transfer', 'Online'])) {
        $errors[] = "Valid payment method is required";
    }
    
    return $errors;
}
?>
