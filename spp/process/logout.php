<?php
/**
 * Logout processing script for PHP School Tuition Payment System (SPP)
 * Handles admin logout and session cleanup
 */

require_once '../config.php';
require_once '../lib/common.php';

// Log logout activity if user is logged in
if (isLoggedIn()) {
    try {
        logActivity($_SESSION['admin_id'], 'logout', 'Admin logged out');
    } catch (Exception $e) {
        error_log("Logout logging error: " . $e->getMessage());
    }
}

// Clear remember me cookie
if (isset($_COOKIE['spp_remember'])) {
    setcookie('spp_remember', '', time() - 3600, '/', '', false, true);
}

// Destroy session
session_unset();
session_destroy();

// Start new session for flash message
session_start();
setFlashMessage('info', 'Anda telah berhasil keluar dari sistem');

// Redirect to login page
redirect('../index.php');
?>
