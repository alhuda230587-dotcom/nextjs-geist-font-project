<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

$pageTitle = $pageTitle ?? APP_NAME;
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">
</head>
<body>
    <?php
    // Display flash messages
    $flashMessage = getFlashMessage();
    if ($flashMessage):
    ?>
    <div class="alert alert-<?php echo $flashMessage['type'] === 'error' ? 'danger' : $flashMessage['type']; ?> alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
        <?php echo htmlspecialchars($flashMessage['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isLoggedIn()): ?>
    <!-- Navigation Bar for Logged In Users -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>admin/dashboard.php">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'students' ? 'active' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>admin/students.php">
                            Data Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'payments' ? 'active' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>admin/payments.php">
                            Pembayaran
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/reports.php?type=monthly">Laporan Bulanan</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/reports.php?type=yearly">Laporan Tahunan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/reports.php?type=outstanding">Tunggakan</a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="user-icon">👤</span>
                            <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/profile.php">Profil</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>admin/settings.php">Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>process/logout.php">Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content Container -->
    <div class="<?php echo isLoggedIn() ? 'container-fluid mt-4' : 'container-fluid'; ?>">
        <?php if (isLoggedIn()): ?>
        <div class="row">
            <!-- Sidebar for larger screens -->
            <div class="col-lg-2 d-none d-lg-block">
                <div class="sidebar bg-light rounded p-3">
                    <h6 class="sidebar-heading text-muted mb-3">MENU UTAMA</h6>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item mb-1">
                            <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" 
                               href="<?php echo BASE_URL; ?>admin/dashboard.php">
                                📊 Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link <?php echo $currentPage === 'students' ? 'active' : ''; ?>" 
                               href="<?php echo BASE_URL; ?>admin/students.php">
                                👥 Data Siswa
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link <?php echo $currentPage === 'payments' ? 'active' : ''; ?>" 
                               href="<?php echo BASE_URL; ?>admin/payments.php">
                                💰 Pembayaran
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading text-muted mb-3 mt-4">LAPORAN</h6>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/reports.php?type=monthly">
                                📈 Bulanan
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/reports.php?type=yearly">
                                📊 Tahunan
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/reports.php?type=outstanding">
                                ⚠️ Tunggakan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-10">
        <?php endif; ?>
