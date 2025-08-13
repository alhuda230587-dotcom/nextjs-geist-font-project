<?php
/**
 * Login processing script for PHP School Tuition Payment System (SPP)
 * Handles admin authentication and session management
 */

require_once '../config.php';
require_once '../lib/common.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php?error=invalid_request');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    redirect('../index.php?error=invalid_token');
}

// Get and sanitize input
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']);

// Validate input
if (empty($username) || empty($password)) {
    redirect('../index.php?error=invalid_credentials&username=' . urlencode($username));
}

try {
    // Attempt authentication
    if (authenticateAdmin($username, $password)) {
        // Set remember me cookie if requested
        if ($rememberMe) {
            $cookieValue = base64_encode($username . ':' . time());
            setcookie('spp_remember', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', false, true); // 30 days
        }
        
        // Set success message
        setFlashMessage('success', 'Selamat datang, ' . $_SESSION['admin_name'] . '!');
        
        // Redirect to dashboard
        redirect('../admin/dashboard.php');
    } else {
        // Authentication failed
        redirect('../index.php?error=invalid_credentials&username=' . urlencode($username));
    }
} catch (Exception $e) {
    // Log error
    error_log("Login error: " . $e->getMessage());
    
    // Redirect with generic error
    redirect('../index.php?error=system_error');
}
?>
