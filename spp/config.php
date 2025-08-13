<?php
/**
 * Configuration file for PHP School Tuition Payment System (SPP)
 * Contains database configuration and application settings
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spp_db');

// Application Configuration
define('APP_NAME', 'Sistem Pembayaran SPP');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:8000/spp/');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 2097152); // 2MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Pagination Configuration
define('RECORDS_PER_PAGE', 10);

// Date and Time Configuration
date_default_timezone_set('Asia/Jakarta');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');

// Currency Configuration
define('CURRENCY_SYMBOL', 'Rp');
define('CURRENCY_FORMAT', 'id_ID');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to format currency
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 0, ',', '.');
}

// Function to format date
function formatDate($date, $format = DATE_FORMAT) {
    return date($format, strtotime($date));
}

// Function to get current month in YYYY-MM format
function getCurrentMonth() {
    return date('Y-m');
}

// Function to get month name in Indonesian
function getMonthName($month) {
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    return $months[$month] ?? $month;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Function to check session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        redirect('index.php?error=session_expired');
    }
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Function to get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number (Indonesian format)
function isValidPhone($phone) {
    return preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $phone);
}

// Function to generate student ID
function generateStudentID() {
    return date('Y') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}
?>
