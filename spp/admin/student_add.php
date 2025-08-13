<?php
/**
 * Add Student Page for PHP School Tuition Payment System (SPP)
 * Form to add new student records
 */

require_once '../config.php';
require_once '../lib/common.php';

// Require admin login
requireLogin();

// Generate new student ID
$newStudentId = generateStudentID();

$pageTitle = 'Tambah Siswa - ' . APP_NAME;
?>

<?php include '../include/header.php'; ?>

<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Tambah Siswa Baru</h1>
            <p class="text-muted mb-0">Masukkan data siswa baru ke dalam sistem</p>
        </div>
        <div>
            <a href="students.php" class="btn btn-outline-secondary">
                ← Kembali ke Daftar Siswa
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📝 Formulir Data Siswa</h5>
                </div>
                <div class="card-body">
                    <form id="studentForm" method="POST" action="../process/add_student.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <!-- Basic Information -->
                        <div class="section-title">Informasi Dasar</div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="student_id" 
                                       name="student_id" 
                                       value="<?php echo htmlspecialchars($newStudentId); ?>"
                                       placeholder="Contoh: 2024001"
                                       required>
                                <div class="form-text">NIS harus unik untuk setiap siswa</div>
                                <div class="invalid-feedback">NIS harus diisi</div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Masukkan nama lengkap siswa"
                                       required>
                                <div class="invalid-feedback">Nama lengkap harus diisi</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="class" class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" id="class" name="class" required>
                                    <option value="">Pilih Kelas</option>
                                    <optgroup label="Kelas X">
                                        <option value="X-A">X-A</option>
                                        <option value="X-B">X-B</option>
                                        <option value="X-C">X-C</option>
                                        <option value="X-D">X-D</option>
                                    </optgroup>
                                    <optgroup label="Kelas XI">
                                        <option value="XI-A">XI-A</option>
                                        <option value="XI-B">XI-B</option>
                                        <option value="XI-C">XI-C</option>
                                        <option value="XI-D">XI-D</option>
                                    </optgroup>
                                    <optgroup label="Kelas XII">
                                        <option value="XII-A">XII-A</option>
                                        <option value="XII-B">XII-B</option>
                                        <option value="XII-C">XII-C</option>
                                        <option value="XII-D">XII-D</option>
                                    </optgroup>
                                </select>
                                <div class="invalid-feedback">Kelas harus dipilih</div>
                            </div>
                            <div class="col-md-6">
                                <label for="monthly_fee" class="form-label">Biaya SPP Bulanan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control currency-input" 
                                           id="monthly_fee" 
                                           name="monthly_fee" 
                                           value="500000"
                                           min="0"
                                           step="1000"
                                           placeholder="500000"
                                           required>
                                </div>
                                <div class="form-text">Biaya SPP per bulan dalam Rupiah</div>
                                <div class="invalid-feedback">Biaya SPP harus diisi dengan nilai yang valid</div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="section-title">Informasi Kontak</div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon Siswa</label>
                                <input type="tel" 
                                       class="form-control phone-input" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="08123456789">
                                <div class="form-text">Format: 08xxxxxxxxxx</div>
                                <div class="invalid-feedback">Format nomor telepon tidak valid</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Siswa</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="siswa@email.com">
                                <div class="form-text">Email untuk komunikasi dengan siswa</div>
                                <div class="invalid-feedback">Format email tidak valid</div>
                            </div>
                        </div>

                        <!-- Parent Information -->
                        <div class="section-title">Informasi Orang Tua/Wali</div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="parent_name" class="form-label">Nama Orang Tua/Wali</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="parent_name" 
                                       name="parent_name" 
                                       placeholder="Masukkan nama orang tua/wali">
                                <div class="form-text">Nama lengkap orang tua atau wali siswa</div>
                            </div>
                            <div class="col-md-6">
                                <label for="parent_phone" class="form-label">Nomor Telepon Orang Tua/Wali</label>
                                <input type="tel" 
                                       class="form-control phone-input" 
                                       id="parent_phone" 
                                       name="parent_phone" 
                                       placeholder="08123456789">
                                <div class="form-text">Nomor telepon untuk komunikasi dengan orang tua</div>
                                <div class="invalid-feedback">Format nomor telepon tidak valid</div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      placeholder="Masukkan alamat lengkap siswa"></textarea>
                            <div class="form-text">Alamat tempat tinggal siswa</div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status Siswa</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" selected>Aktif</option>
                                <option value="Inactive">Tidak Aktif</option>
                            </select>
                            <div class="form-text">Status keaktifan siswa dalam sistem</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="students.php" class="btn btn-outline-secondary">
                                ← Batal
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    🔄 Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    💾 Simpan Data Siswa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💡 Bantuan</h5>
                </div>
                <div class="card-body">
                    <h6>Petunjuk Pengisian:</h6>
                    <ul class="small">
                        <li><strong>NIS:</strong> Nomor unik untuk setiap siswa. Sistem akan generate otomatis, tapi bisa diubah.</li>
                        <li><strong>Nama:</strong> Masukkan nama lengkap sesuai dokumen resmi.</li>
                        <li><strong>Kelas:</strong> Pilih kelas sesuai dengan tingkat pendidikan siswa.</li>
                        <li><strong>Biaya SPP:</strong> Biaya bulanan yang harus dibayar siswa.</li>
                        <li><strong>Kontak:</strong> Informasi kontak untuk komunikasi.</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Catatan Penting:</h6>
                    <div class="alert alert-info small">
                        <strong>Field yang wajib diisi:</strong> NIS, Nama, Kelas, dan Biaya SPP.
                        Field lainnya bersifat opsional tapi disarankan untuk diisi.
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">👁️ Preview Data</h5>
                </div>
                <div class="card-body">
                    <div id="studentPreview">
                        <p class="text-muted small">Preview akan muncul saat Anda mengisi form</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form validation
    $('#studentForm').on('submit', function(e) {
        if (!validateStudentForm()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });
    
    // Real-time validation
    $('#student_id').on('blur', function() {
        const studentId = $(this).val().trim();
        if (studentId) {
            checkStudentIdAvailability(studentId);
        }
    });
    
    // Phone number formatting
    $('.phone-input').on('input', function() {
        formatPhoneInput(this);
    });
    
    // Currency formatting
    $('#monthly_fee').on('input', function() {
        const value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            $(this).val(value);
        }
    });
    
    // Real-time preview
    $('#studentForm input, #studentForm select, #studentForm textarea').on('input change', function() {
        updatePreview();
    });
    
    // Generate new student ID
    $('#generateNewId').on('click', function(e) {
        e.preventDefault();
        generateNewStudentId();
    });
    
    // Reset form confirmation
    $('button[type="reset"]').on('click', function(e) {
        e.preventDefault();
        showConfirmDialog(
            'Konfirmasi Reset',
            'Apakah Anda yakin ingin mengosongkan semua field form?',
            'warning',
            function() {
                $('#studentForm')[0].reset();
                updatePreview();
            }
        );
    });
    
    // Initial preview update
    updatePreview();
});

function validateStudentForm() {
    let isValid = true;
    const errors = [];
    
    // Reset validation states
    $('.form-control, .form-select').removeClass('is-invalid');
    
    // Validate student ID
    const studentId = $('#student_id').val().trim();
    if (!studentId) {
        $('#student_id').addClass('is-invalid');
        errors.push('NIS siswa harus diisi');
        isValid = false;
    }
    
    // Validate name
    const name = $('#name').val().trim();
    if (!name) {
        $('#name').addClass('is-invalid');
        errors.push('Nama siswa harus diisi');
        isValid = false;
    }
    
    // Validate class
    const studentClass = $('#class').val();
    if (!studentClass) {
        $('#class').addClass('is-invalid');
        errors.push('Kelas harus dipilih');
        isValid = false;
    }
    
    // Validate monthly fee
    const monthlyFee = parseFloat($('#monthly_fee').val());
    if (!monthlyFee || monthlyFee <= 0) {
        $('#monthly_fee').addClass('is-invalid');
        errors.push('Biaya SPP harus diisi dengan nilai yang valid');
        isValid = false;
    }
    
    // Validate email if provided
    const email = $('#email').val().trim();
    if (email && !isValidEmail(email)) {
        $('#email').addClass('is-invalid');
        errors.push('Format email tidak valid');
        isValid = false;
    }
    
    // Validate phone numbers if provided
    const phone = $('#phone').val().trim();
    if (phone && !isValidPhone(phone)) {
        $('#phone').addClass('is-invalid');
        errors.push('Format nomor telepon siswa tidak valid');
        isValid = false;
    }
    
    const parentPhone = $('#parent_phone').val().trim();
    if (parentPhone && !isValidPhone(parentPhone)) {
        $('#parent_phone').addClass('is-invalid');
        errors.push('Format nomor telepon orang tua tidak valid');
        isValid = false;
    }
    
    if (!isValid) {
        showAlert('error', 'Validasi Gagal', errors.join('<br>'));
        // Focus on first invalid field
        $('.is-invalid').first().focus();
    }
    
    return isValid;
}

function checkStudentIdAvailability(studentId) {
    $.ajax({
        url: '../ajax/check_student_id.php',
        method: 'POST',
        data: { student_id: studentId },
        dataType: 'json',
        success: function(response) {
            if (response.exists) {
                $('#student_id').addClass('is-invalid');
                $('#student_id').siblings('.invalid-feedback').text('NIS sudah digunakan oleh siswa lain');
            } else {
                $('#student_id').removeClass('is-invalid');
            }
        },
        error: function() {
            console.log('Error checking student ID availability');
        }
    });
}

function generateNewStudentId() {
    $.ajax({
        url: '../ajax/generate_student_id.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#student_id').val(response.student_id);
                updatePreview();
            }
        },
        error: function() {
            console.log('Error generating new student ID');
        }
    });
}

function updatePreview() {
    const data = {
        student_id: $('#student_id').val(),
        name: $('#name').val(),
        class: $('#class').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        parent_name: $('#parent_name').val(),
        parent_phone: $('#parent_phone').val(),
        monthly_fee: $('#monthly_fee').val(),
        status: $('#status').val()
    };
    
    let previewHtml = '';
    
    if (data.name || data.student_id) {
        previewHtml += '<div class="border rounded p-2 bg-light">';
        
        if (data.name) {
            previewHtml += `<h6 class="mb-1">${data.name}</h6>`;
        }
        
        if (data.student_id) {
            previewHtml += `<small class="text-muted">NIS: ${data.student_id}</small><br>`;
        }
        
        if (data.class) {
            previewHtml += `<span class="badge bg-info">${data.class}</span><br>`;
        }
        
        if (data.monthly_fee) {
            const fee = parseFloat(data.monthly_fee);
            if (!isNaN(fee)) {
                previewHtml += `<small class="text-success">SPP: Rp ${fee.toLocaleString('id-ID')}</small><br>`;
            }
        }
        
        if (data.phone) {
            previewHtml += `<small>📱 ${data.phone}</small><br>`;
        }
        
        if (data.email) {
            previewHtml += `<small>📧 ${data.email}</small><br>`;
        }
        
        if (data.parent_name) {
            previewHtml += `<small>👨‍👩‍👧‍👦 ${data.parent_name}</small><br>`;
        }
        
        previewHtml += '</div>';
    } else {
        previewHtml = '<p class="text-muted small">Preview akan muncul saat Anda mengisi form</p>';
    }
    
    $('#studentPreview').html(previewHtml);
}

function formatPhoneInput(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value.startsWith('0')) {
        value = value.substring(1);
    }
    if (value.length > 0) {
        value = '0' + value;
    }
    input.value = value;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
    return phoneRegex.test(phone);
}
</script>

<?php include '../include/footer.php'; ?>
