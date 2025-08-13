<?php
/**
 * Login page for PHP School Tuition Payment System (SPP)
 * Main entry point for admin authentication
 */

require_once 'config.php';
require_once 'lib/common.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('admin/dashboard.php');
}

// Handle login error messages
$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_credentials':
            $error = 'Username atau password salah';
            break;
        case 'session_expired':
            $error = 'Sesi Anda telah berakhir, silakan login kembali';
            break;
        case 'access_denied':
            $error = 'Akses ditolak, silakan login terlebih dahulu';
            break;
        default:
            $error = 'Terjadi kesalahan, silakan coba lagi';
    }
}

$pageTitle = 'Login - ' . APP_NAME;
?>

<?php include 'include/header.php'; ?>

<div class="login-container">
    <div class="login-card fade-in">
        <div class="login-header">
            <h1><?php echo APP_NAME; ?></h1>
            <p class="text-muted">Sistem Pembayaran SPP Sekolah</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Login Gagal!</strong><br>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="process/login_process.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       placeholder="Masukkan username"
                       value="<?php echo htmlspecialchars($_GET['username'] ?? ''); ?>"
                       required 
                       autofocus>
                <div class="invalid-feedback">
                    Username harus diisi
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan password"
                           required>
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            id="togglePassword"
                            data-bs-toggle="tooltip"
                            title="Tampilkan/Sembunyikan Password">
                        👁️
                    </button>
                </div>
                <div class="invalid-feedback">
                    Password harus diisi
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label class="form-check-label" for="remember_me">
                    Ingat saya
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    Masuk
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                Lupa password? Hubungi administrator sekolah
            </small>
        </div>

        <hr class="my-4">

        <div class="text-center">
            <h6 class="text-muted mb-3">Demo Login</h6>
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body py-2">
                            <small>
                                <strong>Username:</strong> admin<br>
                                <strong>Password:</strong> admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        
        // Update button text
        $(this).text(type === 'password' ? '👁️' : '🙈');
    });
    
    // Form validation
    $('#loginForm').on('submit', function(e) {
        let isValid = true;
        
        // Reset previous validation states
        $('.form-control').removeClass('is-invalid');
        
        // Validate username
        const username = $('#username').val().trim();
        if (!username) {
            $('#username').addClass('is-invalid');
            isValid = false;
        }
        
        // Validate password
        const password = $('#password').val();
        if (!password) {
            $('#password').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
    });
    
    // Auto-fill demo credentials
    $('.demo-login').on('click', function(e) {
        e.preventDefault();
        $('#username').val('admin');
        $('#password').val('admin123');
        $('#username').focus();
    });
    
    // Remember username from localStorage
    const rememberedUsername = localStorage.getItem('spp_username');
    if (rememberedUsername && !$('#username').val()) {
        $('#username').val(rememberedUsername);
        $('#remember_me').prop('checked', true);
    }
    
    // Save username to localStorage if remember me is checked
    $('#loginForm').on('submit', function() {
        if ($('#remember_me').is(':checked')) {
            localStorage.setItem('spp_username', $('#username').val());
        } else {
            localStorage.removeItem('spp_username');
        }
    });
    
    // Focus on first empty field
    if (!$('#username').val()) {
        $('#username').focus();
    } else if (!$('#password').val()) {
        $('#password').focus();
    }
    
    // Handle Enter key in form fields
    $('#username, #password').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#loginForm').submit();
        }
    });
    
    // Clear error messages after user starts typing
    $('#username, #password').on('input', function() {
        $(this).removeClass('is-invalid');
        $('.alert-danger').fadeOut();
    });
});

// Show system information in console
console.log('%c' + '<?php echo APP_NAME; ?>', 'color: #0d6efd; font-size: 16px; font-weight: bold;');
console.log('Version: <?php echo APP_VERSION; ?>');
console.log('Developed for School Management System');
</script>

<?php include 'include/footer.php'; ?>
