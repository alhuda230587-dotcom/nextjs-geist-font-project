<?php
/**
 * Add Student Processing Script for PHP School Tuition Payment System (SPP)
 * Handles the creation of new student records
 */

require_once '../config.php';
require_once '../lib/common.php';

// Require admin login
requireLogin();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/students.php?error=invalid_request');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    redirect('../admin/student_add.php?error=invalid_token');
}

// Get and sanitize input data
$studentData = [
    'student_id' => sanitizeInput($_POST['student_id'] ?? ''),
    'name' => sanitizeInput($_POST['name'] ?? ''),
    'class' => sanitizeInput($_POST['class'] ?? ''),
    'phone' => sanitizeInput($_POST['phone'] ?? ''),
    'email' => sanitizeInput($_POST['email'] ?? ''),
    'parent_name' => sanitizeInput($_POST['parent_name'] ?? ''),
    'parent_phone' => sanitizeInput($_POST['parent_phone'] ?? ''),
    'address' => sanitizeInput($_POST['address'] ?? ''),
    'monthly_fee' => floatval($_POST['monthly_fee'] ?? 0),
    'status' => sanitizeInput($_POST['status'] ?? 'Active')
];

try {
    // Validate student data
    $validationErrors = validateStudentData($studentData);
    
    if (!empty($validationErrors)) {
        setFlashMessage('error', 'Validasi gagal: ' . implode(', ', $validationErrors));
        redirect('../admin/student_add.php?' . http_build_query($studentData));
    }
    
    // Add student to database
    $studentId = addStudent($studentData);
    
    if ($studentId) {
        setFlashMessage('success', 'Data siswa berhasil ditambahkan dengan ID: ' . $studentId);
        redirect('../admin/students.php');
    } else {
        throw new Exception('Gagal menambahkan data siswa');
    }
    
} catch (Exception $e) {
    error_log("Add student error: " . $e->getMessage());
    setFlashMessage('error', 'Terjadi kesalahan: ' . $e->getMessage());
    redirect('../admin/student_add.php?' . http_build_query($studentData));
}
?>
