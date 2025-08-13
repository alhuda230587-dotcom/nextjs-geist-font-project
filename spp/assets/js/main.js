/**
 * Main JavaScript file for PHP School Tuition Payment System (SPP)
 * Contains common functions and interactive features
 */

$(document).ready(function() {
    // Initialize all components
    initializeComponents();
    
    // Set up event listeners
    setupEventListeners();
    
    // Initialize form validations
    initializeValidations();
});

/**
 * Initialize all JavaScript components
 */
function initializeComponents() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Initialize date pickers
    initializeDatePickers();
    
    // Initialize currency formatters
    initializeCurrencyFormatters();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize table sorting
    initializeTableSorting();
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
    // Confirm delete actions
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const itemName = $(this).data('name') || 'item ini';
        
        showConfirmDialog(
            'Konfirmasi Hapus',
            `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`,
            'danger',
            function() {
                window.location.href = url;
            }
        );
    });
    
    // Handle form submissions with loading states
    $(document).on('submit', 'form', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
        
        // Re-enable button after 10 seconds as fallback
        setTimeout(function() {
            submitBtn.prop('disabled', false).text(originalText);
        }, 10000);
    });
    
    // Auto-format currency inputs
    $(document).on('input', '.currency-input', function() {
        formatCurrencyInput(this);
    });
    
    // Auto-format phone number inputs
    $(document).on('input', '.phone-input', function() {
        formatPhoneInput(this);
    });
    
    // Handle student selection for payments
    $(document).on('change', '#student_id', function() {
        const studentId = $(this).val();
        if (studentId) {
            loadStudentInfo(studentId);
        }
    });
    
    // Handle month selection for payment reports
    $(document).on('change', '#payment_month', function() {
        const month = $(this).val();
        if (month) {
            updatePaymentSummary(month);
        }
    });
    
    // Print functionality
    $(document).on('click', '.btn-print', function(e) {
        e.preventDefault();
        window.print();
    });
    
    // Export functionality
    $(document).on('click', '.btn-export', function(e) {
        e.preventDefault();
        const format = $(this).data('format') || 'excel';
        const url = $(this).attr('href');
        
        showLoadingDialog('Mengekspor data...');
        
        // Create hidden iframe for download
        const iframe = $('<iframe>').hide().appendTo('body');
        iframe.attr('src', url);
        
        // Hide loading dialog after 3 seconds
        setTimeout(function() {
            hideLoadingDialog();
            iframe.remove();
        }, 3000);
    });
}

/**
 * Initialize form validations
 */
function initializeValidations() {
    // Student form validation
    $('#studentForm').on('submit', function(e) {
        if (!validateStudentForm()) {
            e.preventDefault();
        }
    });
    
    // Payment form validation
    $('#paymentForm').on('submit', function(e) {
        if (!validatePaymentForm()) {
            e.preventDefault();
        }
    });
    
    // Login form validation
    $('#loginForm').on('submit', function(e) {
        if (!validateLoginForm()) {
            e.preventDefault();
        }
    });
}

/**
 * Initialize date pickers
 */
function initializeDatePickers() {
    $('.date-picker').each(function() {
        $(this).attr('type', 'date');
        
        // Set max date to today for payment dates
        if ($(this).hasClass('payment-date')) {
            $(this).attr('max', new Date().toISOString().split('T')[0]);
        }
    });
}

/**
 * Initialize currency formatters
 */
function initializeCurrencyFormatters() {
    $('.currency-display').each(function() {
        const value = parseFloat($(this).text().replace(/[^\d.-]/g, ''));
        if (!isNaN(value)) {
            $(this).text(formatCurrency(value));
        }
    });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    let searchTimeout;
    
    $('.search-input').on('input', function() {
        const searchTerm = $(this).val();
        const targetTable = $(this).data('target');
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch(searchTerm, targetTable);
        }, 500);
    });
}

/**
 * Initialize table sorting
 */
function initializeTableSorting() {
    $('.sortable-table th[data-sort]').addClass('sortable').css('cursor', 'pointer');
    
    $('.sortable-table th[data-sort]').on('click', function() {
        const column = $(this).data('sort');
        const table = $(this).closest('table');
        const currentOrder = $(this).data('order') || 'asc';
        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        
        // Remove sorting indicators from other columns
        table.find('th[data-sort]').removeClass('sort-asc sort-desc').data('order', '');
        
        // Add sorting indicator to current column
        $(this).addClass('sort-' + newOrder).data('order', newOrder);
        
        // Sort table rows
        sortTable(table, column, newOrder);
    });
}

/**
 * Validation Functions
 */

function validateStudentForm() {
    let isValid = true;
    const errors = [];
    
    // Validate student ID
    const studentId = $('#student_id').val().trim();
    if (!studentId) {
        errors.push('NIS siswa harus diisi');
        isValid = false;
    }
    
    // Validate name
    const name = $('#name').val().trim();
    if (!name) {
        errors.push('Nama siswa harus diisi');
        isValid = false;
    }
    
    // Validate class
    const studentClass = $('#class').val().trim();
    if (!studentClass) {
        errors.push('Kelas harus diisi');
        isValid = false;
    }
    
    // Validate email if provided
    const email = $('#email').val().trim();
    if (email && !isValidEmail(email)) {
        errors.push('Format email tidak valid');
        isValid = false;
    }
    
    // Validate phone if provided
    const phone = $('#phone').val().trim();
    if (phone && !isValidPhone(phone)) {
        errors.push('Format nomor telepon tidak valid');
        isValid = false;
    }
    
    // Validate monthly fee
    const monthlyFee = parseFloat($('#monthly_fee').val());
    if (!monthlyFee || monthlyFee <= 0) {
        errors.push('Biaya bulanan harus diisi dengan nilai yang valid');
        isValid = false;
    }
    
    if (!isValid) {
        showAlert('error', 'Validasi Gagal', errors.join('<br>'));
    }
    
    return isValid;
}

function validatePaymentForm() {
    let isValid = true;
    const errors = [];
    
    // Validate student selection
    const studentId = $('#student_id').val();
    if (!studentId) {
        errors.push('Siswa harus dipilih');
        isValid = false;
    }
    
    // Validate amount
    const amount = parseFloat($('#amount').val());
    if (!amount || amount <= 0) {
        errors.push('Jumlah pembayaran harus diisi dengan nilai yang valid');
        isValid = false;
    }
    
    // Validate payment date
    const paymentDate = $('#payment_date').val();
    if (!paymentDate) {
        errors.push('Tanggal pembayaran harus diisi');
        isValid = false;
    }
    
    // Validate payment month
    const paymentMonth = $('#payment_month').val();
    if (!paymentMonth) {
        errors.push('Bulan pembayaran harus dipilih');
        isValid = false;
    }
    
    if (!isValid) {
        showAlert('error', 'Validasi Gagal', errors.join('<br>'));
    }
    
    return isValid;
}

function validateLoginForm() {
    let isValid = true;
    const errors = [];
    
    const username = $('#username').val().trim();
    if (!username) {
        errors.push('Username harus diisi');
        isValid = false;
    }
    
    const password = $('#password').val();
    if (!password) {
        errors.push('Password harus diisi');
        isValid = false;
    }
    
    if (!isValid) {
        showAlert('error', 'Login Gagal', errors.join('<br>'));
    }
    
    return isValid;
}

/**
 * Utility Functions
 */

function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = value;
    }
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

function performSearch(searchTerm, targetTable) {
    if (!targetTable) return;
    
    const table = $('#' + targetTable);
    const rows = table.find('tbody tr');
    
    if (!searchTerm) {
        rows.show();
        return;
    }
    
    rows.each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        
        if (text.includes(searchTerm.toLowerCase())) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function sortTable(table, column, order) {
    const tbody = table.find('tbody');
    const rows = tbody.find('tr').toArray();
    
    rows.sort(function(a, b) {
        const aValue = $(a).find(`td[data-sort="${column}"]`).text().trim();
        const bValue = $(b).find(`td[data-sort="${column}"]`).text().trim();
        
        // Try to parse as numbers first
        const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return order === 'asc' ? aNum - bNum : bNum - aNum;
        }
        
        // Sort as strings
        if (order === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    tbody.empty().append(rows);
}

function loadStudentInfo(studentId) {
    $.ajax({
        url: 'ajax/get_student_info.php',
        method: 'GET',
        data: { id: studentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const student = response.data;
                $('#amount').val(student.monthly_fee);
                $('#student_info').html(`
                    <div class="alert alert-info">
                        <strong>${student.name}</strong><br>
                        Kelas: ${student.class}<br>
                        Biaya Bulanan: ${formatCurrency(student.monthly_fee)}
                    </div>
                `);
            }
        },
        error: function() {
            showAlert('error', 'Error', 'Gagal memuat informasi siswa');
        }
    });
}

function updatePaymentSummary(month) {
    $.ajax({
        url: 'ajax/get_payment_summary.php',
        method: 'GET',
        data: { month: month },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#payment_summary').html(response.html);
            }
        },
        error: function() {
            showAlert('error', 'Error', 'Gagal memuat ringkasan pembayaran');
        }
    });
}

/**
 * Dialog Functions
 */

function showAlert(type, title, message) {
    const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${title}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert to top of page
    $('body').prepend(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

function showConfirmDialog(title, message, type, callback) {
    const buttonClass = type === 'danger' ? 'btn-danger' : 'btn-primary';
    const buttonText = type === 'danger' ? 'Hapus' : 'Konfirmasi';
    
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn ${buttonClass}" id="confirmButton">${buttonText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    $('#confirmModal').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
    
    // Handle confirm button click
    $('#confirmButton').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') {
            callback();
        }
    });
}

function showLoadingDialog(message) {
    const modalHtml = `
        <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>${message}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    $('#loadingModal').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
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

/**
 * Export Functions
 */

function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, filename + '.xlsx');
}

function exportToPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    const opt = {
        margin: 1,
        filename: filename + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}

/**
 * Print Functions
 */

function printTable(tableId) {
    const table = document.getElementById(tableId);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            ${table.outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

/**
 * Local Storage Functions
 */

function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (e) {
        console.error('Failed to save to localStorage:', e);
    }
}

function loadFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Failed to load from localStorage:', e);
        return null;
    }
}

function removeFromLocalStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.error('Failed to remove from localStorage:', e);
    }
}
