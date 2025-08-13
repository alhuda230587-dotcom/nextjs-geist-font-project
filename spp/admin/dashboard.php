<?php
/**
 * Admin Dashboard for PHP School Tuition Payment System (SPP)
 * Main dashboard with statistics and overview
 */

require_once '../config.php';
require_once '../lib/common.php';

// Require admin login
requireLogin();

// Get dashboard statistics
$stats = getDashboardStats();

// Get recent activities
$recentActivities = getRecentActivities(5);

// Get current month info
$currentMonth = getCurrentMonth();
$currentMonthName = getMonthName(date('m'));
$currentYear = date('Y');

$pageTitle = 'Dashboard - ' . APP_NAME;
?>

<?php include '../include/header.php'; ?>

<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="text-muted mb-0">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
        </div>
        <div class="text-end">
            <small class="text-muted">
                <?php echo formatDate(date('Y-m-d'), 'l, d F Y'); ?><br>
                <span id="current-time"><?php echo date('H:i:s'); ?></span>
            </small>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($stats['total_students'] ?? 0); ?></div>
                    <div class="stats-label">Total Siswa Aktif</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($stats['payments_this_month'] ?? 0); ?></div>
                    <div class="stats-label">Pembayaran Bulan Ini</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($stats['pending_payments'] ?? 0); ?></div>
                    <div class="stats-label">Pembayaran Pending</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($stats['overdue_payments'] ?? 0); ?></div>
                    <div class="stats-label">Pembayaran Terlambat</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💰 Pendapatan Bulan <?php echo $currentMonthName . ' ' . $currentYear; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="text-success mb-0 text-currency">
                                <?php echo formatCurrency($stats['revenue_this_month'] ?? 0); ?>
                            </h2>
                            <p class="text-muted mb-0">Total pendapatan dari pembayaran SPP</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="reports.php?type=monthly&month=<?php echo $currentMonth; ?>" class="btn btn-outline-primary">
                                Lihat Detail Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="student_add.php" class="btn btn-primary w-100">
                                <div class="mb-1">👥</div>
                                Tambah Siswa
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="payments.php?action=add" class="btn btn-success w-100">
                                <div class="mb-1">💰</div>
                                Input Pembayaran
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="students.php" class="btn btn-info w-100">
                                <div class="mb-1">📋</div>
                                Data Siswa
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="reports.php" class="btn btn-warning w-100">
                                <div class="mb-1">📊</div>
                                Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📝 Aktivitas Terbaru</h5>
                    <small class="text-muted">5 aktivitas terakhir</small>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivities)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['admin_name']); ?></h6>
                                    <p class="mb-1 small"><?php echo htmlspecialchars($activity['description']); ?></p>
                                    <small class="text-muted">
                                        <?php echo formatDate($activity['created_at'], 'd/m/Y H:i'); ?>
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo ucfirst($activity['action']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <div class="mb-2">📝</div>
                        <p class="mb-0">Belum ada aktivitas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📈 Status Pembayaran Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get payment status breakdown for current month
                    $paymentStats = [
                        'paid' => getCount('payments', ['payment_month = ?', 'status = ?'], [$currentMonth, 'Paid']),
                        'pending' => getCount('payments', ['payment_month = ?', 'status = ?'], [$currentMonth, 'Pending']),
                        'overdue' => getCount('payments', ['payment_month = ?', 'status = ?'], [$currentMonth, 'Overdue'])
                    ];
                    $totalPayments = array_sum($paymentStats);
                    ?>
                    
                    <?php if ($totalPayments > 0): ?>
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Progress bars -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-success">Lunas</span>
                                    <span><?php echo $paymentStats['paid']; ?> (<?php echo round(($paymentStats['paid'] / $totalPayments) * 100, 1); ?>%)</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo ($paymentStats['paid'] / $totalPayments) * 100; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-warning">Pending</span>
                                    <span><?php echo $paymentStats['pending']; ?> (<?php echo round(($paymentStats['pending'] / $totalPayments) * 100, 1); ?>%)</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: <?php echo ($paymentStats['pending'] / $totalPayments) * 100; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-danger">Terlambat</span>
                                    <span><?php echo $paymentStats['overdue']; ?> (<?php echo round(($paymentStats['overdue'] / $totalPayments) * 100, 1); ?>%)</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: <?php echo ($paymentStats['overdue'] / $totalPayments) * 100; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1"><?php echo $totalPayments; ?></h3>
                                <p class="text-muted mb-0">Total Pembayaran</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <div class="mb-2">📊</div>
                        <p class="mb-0">Belum ada data pembayaran untuk bulan ini</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update current time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        $('#current-time').text(timeString);
    }
    
    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);
    
    // Add hover effects to stats cards
    $('.stats-card').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );
    
    // Add click tracking for quick actions
    $('.btn').on('click', function() {
        const action = $(this).text().trim();
        console.log('Quick action clicked:', action);
    });
    
    // Auto-refresh dashboard data every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutes
});
</script>

<?php include '../include/footer.php'; ?>
