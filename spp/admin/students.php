<?php
/**
 * Students Management Page for PHP School Tuition Payment System (SPP)
 * Display, search, and manage student records
 */

require_once '../config.php';
require_once '../lib/common.php';

// Require admin login
requireLogin();

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = RECORDS_PER_PAGE;

// Get students data
$students = getStudents($search, $page, $perPage);

// Get total count for pagination
$totalStudents = getCount('students', 
    !empty($search) ? ['(name LIKE ? OR student_id LIKE ? OR class LIKE ? OR parent_name LIKE ?)'] : [], 
    !empty($search) ? ["%$search%", "%$search%", "%$search%", "%$search%"] : []
);
$totalPages = ceil($totalStudents / $perPage);

$pageTitle = 'Data Siswa - ' . APP_NAME;
?>

<?php include '../include/header.php'; ?>

<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Data Siswa</h1>
            <p class="text-muted mb-0">Kelola data siswa dan informasi pembayaran</p>
        </div>
        <div>
            <a href="student_add.php" class="btn btn-primary">
                <span class="me-1">➕</span>
                Tambah Siswa
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-filter-container">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Cari Siswa</label>
                <input type="text" 
                       class="form-control search-input" 
                       id="search" 
                       name="search" 
                       placeholder="Cari berdasarkan nama, NIS, kelas, atau nama orang tua..."
                       value="<?php echo htmlspecialchars($search); ?>"
                       data-target="studentsTable">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        🔍 Cari
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="students.php" class="btn btn-outline-secondary">
                        🔄 Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                📋 Daftar Siswa 
                <?php if (!empty($search)): ?>
                <small class="text-muted">(hasil pencarian: "<?php echo htmlspecialchars($search); ?>")</small>
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
            <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover sortable-table mb-0" id="studentsTable">
                    <thead>
                        <tr>
                            <th data-sort="student_id">NIS</th>
                            <th data-sort="name">Nama Siswa</th>
                            <th data-sort="class">Kelas</th>
                            <th data-sort="parent_name">Nama Orang Tua</th>
                            <th data-sort="phone">Telepon</th>
                            <th data-sort="monthly_fee">Biaya Bulanan</th>
                            <th data-sort="status">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td data-sort="student_id">
                                <strong><?php echo htmlspecialchars($student['student_id']); ?></strong>
                            </td>
                            <td data-sort="name">
                                <div>
                                    <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                    <?php if (!empty($student['email'])): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-sort="class">
                                <span class="badge bg-info"><?php echo htmlspecialchars($student['class']); ?></span>
                            </td>
                            <td data-sort="parent_name">
                                <div>
                                    <?php echo htmlspecialchars($student['parent_name'] ?: '-'); ?>
                                    <?php if (!empty($student['parent_phone'])): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($student['parent_phone']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-sort="phone">
                                <?php echo htmlspecialchars($student['phone'] ?: '-'); ?>
                            </td>
                            <td data-sort="monthly_fee" class="text-currency">
                                <?php echo formatCurrency($student['monthly_fee']); ?>
                            </td>
                            <td data-sort="status">
                                <span class="badge <?php echo $student['status'] === 'Active' ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $student['status'] === 'Active' ? 'Aktif' : 'Tidak Aktif'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="student_edit.php?id=<?php echo $student['id']; ?>" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Edit Siswa">
                                        ✏️
                                    </a>
                                    <a href="payments.php?student_id=<?php echo $student['id']; ?>" 
                                       class="btn btn-outline-success"
                                       data-bs-toggle="tooltip" 
                                       title="Lihat Pembayaran">
                                        💰
                                    </a>
                                    <a href="process/delete_student.php?id=<?php echo $student['id']; ?>" 
                                       class="btn btn-outline-danger btn-delete"
                                       data-name="<?php echo htmlspecialchars($student['name']); ?>"
                                       data-bs-toggle="tooltip" 
                                       title="Hapus Siswa">
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
                <div class="mb-3" style="font-size: 3rem;">👥</div>
                <h5 class="text-muted">
                    <?php if (!empty($search)): ?>
                        Tidak ada siswa yang ditemukan
                    <?php else: ?>
                        Belum ada data siswa
                    <?php endif; ?>
                </h5>
                <p class="text-muted mb-3">
                    <?php if (!empty($search)): ?>
                        Coba gunakan kata kunci yang berbeda atau <a href="students.php">lihat semua siswa</a>
                    <?php else: ?>
                        Mulai dengan menambahkan siswa baru ke sistem
                    <?php endif; ?>
                </p>
                <?php if (empty($search)): ?>
                <a href="student_add.php" class="btn btn-primary">
                    ➕ Tambah Siswa Pertama
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan <?php echo count($students); ?> dari <?php echo $totalStudents; ?> siswa
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
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
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
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
    // Initialize search functionality
    let searchTimeout;
    $('.search-input').on('input', function() {
        const searchTerm = $(this).val();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                // Auto-submit form for search
                $('form').submit();
            }
        }, 500);
    });
    
    // Export functionality
    $('.btn-export').on('click', function(e) {
        e.preventDefault();
        const format = $(this).data('format');
        
        // Build export URL with current filters
        let exportUrl = 'export/students.php?format=' + format;
        const search = '<?php echo urlencode($search); ?>';
        if (search) {
            exportUrl += '&search=' + search;
        }
        
        // Show loading
        showLoadingDialog('Mengekspor data siswa...');
        
        // Create download link
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'data_siswa_' + new Date().toISOString().split('T')[0] + '.' + (format === 'excel' ? 'xlsx' : 'pdf');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Hide loading after delay
        setTimeout(hideLoadingDialog, 2000);
    });
    
    // Print functionality
    $('.btn-print').on('click', function(e) {
        e.preventDefault();
        printTable('studentsTable');
    });
    
    // Enhanced delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const studentName = $(this).data('name');
        const deleteUrl = $(this).attr('href');
        
        showConfirmDialog(
            'Konfirmasi Hapus Siswa',
            `Apakah Anda yakin ingin menghapus siswa <strong>${studentName}</strong>?<br><br>
            <div class="alert alert-warning mt-2">
                <small><strong>Peringatan:</strong> Tindakan ini akan menghapus semua data terkait siswa termasuk riwayat pembayaran dan tidak dapat dibatalkan.</small>
            </div>`,
            'danger',
            function() {
                window.location.href = deleteUrl;
            }
        );
    });
    
    // Add row hover effects
    $('#studentsTable tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
    
    // Auto-refresh data every 2 minutes
    setTimeout(function() {
        location.reload();
    }, 120000);
});

// Helper function for loading dialog
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
