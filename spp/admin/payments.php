<?php
/**
 * Payments Management Page for PHP School Tuition Payment System (SPP)
 * Display, search, and manage payment records
 */

require_once '../config.php';
require_once '../lib/common.php';

// Require admin login
requireLogin();

// Get search and filter parameters
$search = sanitizeInput($_GET['search'] ?? '');
$status = sanitizeInput($_GET['status'] ?? '');
$month = sanitizeInput($_GET['month'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = RECORDS_PER_PAGE;

// Get payments data
$payments = getPayments($search, $status, $month, $page, $perPage);

// Get total count for pagination
$whereConditions = [];
$whereParams = [];

if (!empty($search)) {
    $whereConditions[] = '(s.name LIKE ? OR s.student_id LIKE ? OR s.class LIKE ?)';
    $whereParams = array_merge($whereParams, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($status)) {
    $whereConditions[] = 'p.status = ?';
    $whereParams[] = $status;
}

if (!empty($month)) {
    $whereConditions[] = 'p.payment_month = ?';
    $whereParams[] = $month;
}

$totalPayments = getCount('payments p JOIN students s ON p.student_id = s.id', $whereConditions, $whereParams);
$totalPages = ceil($totalPayments / $perPage);

// Get available months for filter
$availableMonths = fetchAll("SELECT DISTINCT payment_month FROM payments ORDER BY payment_month DESC LIMIT 12");

$pageTitle = 'Data Pembayaran - ' . APP_NAME;
?>

<?php include '../include/header.php'; ?>

<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Data Pembayaran</h1>
            <p class="text-muted mb-0">Kelola pembayaran SPP siswa</p>
        </div>
        <div>
            <a href="payment_add.php" class="btn btn-primary">
                <span class="me-1">💰</span>
                Input Pembayaran
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-filter-container">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Cari Pembayaran</label>
                <input type="text" 
                       class="form-control search-input" 
                       id="search" 
                       name="search" 
                       placeholder="Cari berdasarkan nama, NIS, atau kelas..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="Paid" <?php echo $status === 'Paid' ? 'selected' : ''; ?>>Lunas</option>
                    <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Overdue" <?php echo $status === 'Overdue' ? 'selected' : ''; ?>>Terlambat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="month" class="form-label">Bulan</label>
                <select class="form-select" id="month" name="month">
                    <option value="">Semua Bulan</option>
                    <?php foreach ($availableMonths as $monthOption): ?>
                    <option value="<?php echo $monthOption['payment_month']; ?>" 
                            <?php echo $month === $monthOption['payment_month'] ? 'selected' : ''; ?>>
                        <?php 
                        $monthParts = explode('-', $monthOption['payment_month']);
                        echo getMonthName($monthParts[1]) . ' ' . $monthParts[0];
                        ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        🔍 Filter
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="payments.php" class="btn btn-outline-secondary">
                        🔄 Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <?php if (!empty($search) || !empty($status) || !empty($month)): ?>
    <div class="row mb-4">
        <?php
        // Get filtered summary
        $summaryConditions = $whereConditions;
        $summaryParams = $whereParams;
        
        $paidCount = getCount('payments p JOIN students s ON p.student_id = s.id', 
            array_merge($summaryConditions, ['p.status = ?']), 
            array_merge($summaryParams, ['Paid']));
        
        $pendingCount = getCount('payments p JOIN students s ON p.student_id = s.id', 
            array_merge($summaryConditions, ['p.status = ?']), 
            array_merge($summaryParams, ['Pending']));
        
        $overdueCount = getCount('payments p JOIN students s ON p.student_id = s.id', 
            array_merge($summaryConditions, ['p.status = ?']), 
            array_merge($summaryParams, ['Overdue']));
        
        // Get total amount
        $totalAmountSql = "SELECT SUM(p.amount) as total FROM payments p JOIN students s ON p.student_id = s.id";
        if (!empty($summaryConditions)) {
            $totalAmountSql .= " WHERE " . implode(' AND ', $summaryConditions) . " AND p.status = 'Paid'";
            $summaryParams[] = 'Paid';
        } else {
            $totalAmountSql .= " WHERE p.status = 'Paid'";
            $summaryParams = ['Paid'];
        }
        $totalAmountResult = fetchSingle($totalAmountSql, $summaryParams);
        $totalAmount = $totalAmountResult['total'] ?? 0;
        ?>
        
        <div class="col-md-3">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($paidCount); ?></div>
                    <div class="stats-label">Pembayaran Lunas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($pendingCount); ?></div>
                    <div class="stats-label">Pembayaran Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card danger">
                <div class="card-body text-center">
                    <div class="stats-number"><?php echo number_format($overdueCount); ?></div>
                    <div class="stats-label">Pembayaran Terlambat</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number text-currency" style="font-size: 1.2rem;">
                        <?php echo formatCurrency($totalAmount); ?>
                    </div>
                    <div class="stats-label">Total Terbayar</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                💰 Daftar Pembayaran
                <?php if (!empty($search) || !empty($status) || !empty($month)): ?>
                <small class="text-muted">(hasil filter)</small>
                <?php endif; ?>
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success btn-export" data-format="excel">
                    📊 Export Excel
                </button>
                <button class="btn btn-sm btn-outline-danger btn-print">
                    🖨️ Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="table table-hover sortable-table mb-0" id="paymentsTable">
                    <thead>
                        <tr>
                            <th data-sort="payment_date">Tanggal</th>
                            <th data-sort="student_name">Siswa</th>
                            <th data-sort="class">Kelas</th>
                            <th data-sort="payment_month">Bulan Bayar</th>
                            <th data-sort="amount">Jumlah</th>
                            <th data-sort="payment_method">Metode</th>
                            <th data-sort="status">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td data-sort="payment_date">
                                <?php echo formatDate($payment['payment_date']); ?>
                            </td>
                            <td data-sort="student_name">
                                <div>
                                    <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong>
                                    <br><small class="text-muted">NIS: <?php echo htmlspecialchars($payment['student_id']); ?></small>
                                </div>
                            </td>
                            <td data-sort="class">
                                <span class="badge bg-info"><?php echo htmlspecialchars($payment['class']); ?></span>
                            </td>
                            <td data-sort="payment_month">
                                <?php 
                                $monthParts = explode('-', $payment['payment_month']);
                                echo getMonthName($monthParts[1]) . ' ' . $monthParts[0];
                                ?>
                            </td>
                            <td data-sort="amount" class="text-currency">
                                <?php echo formatCurrency($payment['amount']); ?>
                            </td>
                            <td data-sort="payment_method">
                                <span class="badge bg-secondary">
                                    <?php 
                                    $methods = [
                                        'Cash' => 'Tunai',
                                        'Transfer' => 'Transfer',
                                        'Online' => 'Online'
                                    ];
                                    echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                                    ?>
                                </span>
                            </td>
                            <td data-sort="status">
                                <span class="badge status-<?php echo strtolower($payment['status']); ?>">
                                    <?php 
                                    $statuses = [
                                        'Paid' => 'Lunas',
                                        'Pending' => 'Pending',
                                        'Overdue' => 'Terlambat'
                                    ];
                                    echo $statuses[$payment['status']] ?? $payment['status'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="payment_edit.php?id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Edit Pembayaran">
                                        ✏️
                                    </a>
                                    <a href="payment_receipt.php?id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-outline-success"
                                       data-bs-toggle="tooltip" 
                                       title="Cetak Kwitansi"
                                       target="_blank">
                                        🧾
                                    </a>
                                    <a href="process/delete_payment.php?id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-outline-danger btn-delete"
                                       data-name="pembayaran <?php echo htmlspecialchars($payment['student_name']); ?>"
                                       data-bs-toggle="tooltip" 
                                       title="Hapus Pembayaran">
                                        🗑️
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3" style="font-size: 3rem;">💰</div>
                <h5 class="text-muted">
                    <?php if (!empty($search) || !empty($status) || !empty($month)): ?>
                        Tidak ada pembayaran yang ditemukan
                    <?php else: ?>
                        Belum ada data pembayaran
                    <?php endif; ?>
                </h5>
                <p class="text-muted mb-3">
                    <?php if (!empty($search) || !empty($status) || !empty($month)): ?>
                        Coba ubah filter pencarian atau <a href="payments.php">lihat semua pembayaran</a>
                    <?php else: ?>
                        Mulai dengan menginput pembayaran SPP siswa
                    <?php endif; ?>
                </p>
                <?php if (empty($search) && empty($status) && empty($month)): ?>
                <a href="payment_add.php" class="btn btn-primary">
                    💰 Input Pembayaran Pertama
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan <?php echo count($payments); ?> dari <?php echo $totalPayments; ?> pembayaran
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo http_build_query(array_filter(['search' => $search, 'status' => $status, 'month' => $month]), '', '&'); ?>">
                                ‹ Sebelumnya
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo http_build_query(array_filter(['search' => $search, 'status' => $status, 'month' => $month]), '', '&'); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo http_build_query(array_filter(['search' => $search, 'status' => $status, 'month' => $month]), '', '&'); ?>">
                                Selanjutnya ›
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#status, #month').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Search with delay
    let searchTimeout;
    $('#search').on('input', function() {
        const searchTerm = $(this).val();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                $('form').submit();
            }
        }, 500);
    });
    
    // Export functionality
    $('.btn-export').on('click', function(e) {
        e.preventDefault();
        const format = $(this).data('format');
        
        // Build export URL with current filters
        let exportUrl = 'export/payments.php?format=' + format;
        const params = new URLSearchParams(window.location.search);
        
        if (params.get('search')) exportUrl += '&search=' + encodeURIComponent(params.get('search'));
        if (params.get('status')) exportUrl += '&status=' + encodeURIComponent(params.get('status'));
        if (params.get('month')) exportUrl += '&month=' + encodeURIComponent(params.get('month'));
        
        // Show loading and download
        showLoadingDialog('Mengekspor data pembayaran...');
        
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'data_pembayaran_' + new Date().toISOString().split('T')[0] + '.' + (format === 'excel' ? 'xlsx' : 'pdf');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(hideLoadingDialog, 2000);
    });
    
    // Print functionality
    $('.btn-print').on('click', function(e) {
        e.preventDefault();
        printTable('paymentsTable');
    });
    
    // Enhanced delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const itemName = $(this).data('name');
        const deleteUrl = $(this).attr('href');
        
        showConfirmDialog(
            'Konfirmasi Hapus Pembayaran',
            `Apakah Anda yakin ingin menghapus ${itemName}?<br><br>
            <div class="alert alert-warning mt-2">
                <small><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.</small>
            </div>`,
            'danger',
            function() {
                window.location.href = deleteUrl;
            }
        );
    });
    
    // Status badge click to filter
    $('.badge.status-paid, .badge.status-pending, .badge.status-overdue').on('click', function() {
        const status = $(this).hasClass('status-paid') ? 'Paid' : 
                      $(this).hasClass('status-pending') ? 'Pending' : 'Overdue';
        $('#status').val(status);
        $('form').submit();
    });
    
    // Month badge click to filter
    $('[data-sort="payment_month"]').on('click', function() {
        const monthText = $(this).text().trim();
        // This would need more complex logic to convert month name back to YYYY-MM format
        // For now, just show the month filter
        $('#month').focus();
    });
});

function showLoadingDialog(message) {
    const modalHtml = `
        <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="spinner-border text-primary mb-3"></div>
                        <div>${message}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoadingDialog() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) {
        modal.hide();
    }
    $('#loadingModal').remove();
}
</script>

<?php include '../include/footer.php'; ?>
