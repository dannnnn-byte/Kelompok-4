<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

$kode_booking = $_GET['kode'] ?? '';

if (empty($kode_booking)) {
    header("Location: index.php");
    exit;
}

// Get pemesanan data
$query = "SELECT p.*, pk.nama_paket, pk.gambar_paket 
          FROM pemesanan p
          JOIN paket_wisata pk ON p.id_paket = pk.id_paket
          WHERE p.kode_booking = '$kode_booking'";
$result = mysqli_query($conn, $query);
$pemesanan = mysqli_fetch_assoc($result);

// Get payment data
$query_payment = "SELECT * FROM pembayaran WHERE kode_booking = '$kode_booking' ORDER BY tanggal_bayar DESC LIMIT 1";
$result_payment = mysqli_query($conn, $query_payment);
$payment = mysqli_fetch_assoc($result_payment);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_bukti'])) {
    $upload_dir = 'uploads/bukti_bayar/';
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
        $file = $_FILES['bukti_bayar'];
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'bukti_' . $kode_booking . '_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Update payment status
                $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
                
                $query_update = "UPDATE pembayaran SET 
                    bukti_bayar = '$new_filename',
                    catatan = '$catatan',
                    status_bayar = 'menunggu_verifikasi',
                    tanggal_konfirmasi = NOW(),
                    nama_pengirim = '{$_POST['nama_pengirim']}',
                    bank_asal = '{$_POST['bank_asal']}'
                    WHERE kode_booking = '$kode_booking'";
                
                if (mysqli_query($conn, $query_update)) {
                    // Update status pemesanan
                    mysqli_query($conn, "UPDATE pemesanan SET status = 'menunggu_verifikasi' WHERE kode_booking = '$kode_booking'");
                    
                    // Insert log
                    mysqli_query($conn, "INSERT INTO booking_log (id_pemesanan, aktivitas, keterangan) 
                                        VALUES ('{$pemesanan['id_pemesanan']}', 'Bukti Bayar Diupload', 'Menunggu verifikasi admin')");
                    
                    $_SESSION['success_message'] = "Bukti pembayaran berhasil diupload! Mohon tunggu verifikasi dari admin.";
                    header("Location: payment_success.php?kode=$kode_booking");
                    exit;
                } else {
                    $error = "Gagal menyimpan data ke database.";
                }
            } else {
                $error = "Gagal mengupload file.";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau PDF.";
        }
    } else {
        $error = "Silakan pilih file bukti pembayaran.";
    }
}
?>

<link rel="stylesheet" href="assets/payment-style.css">

<div class="payment-container">
    <div class="confirmation-wrapper">
        <div class="confirmation-card-full">
            <div class="confirmation-icon">
                <i class="bi bi-upload"></i>
            </div>
            
            <h2>Upload Bukti Pembayaran</h2>
            <p class="subtitle">Upload bukti pembayaran Anda untuk verifikasi</p>
            
            <div class="booking-info-box">
                <div class="info-row">
                    <span class="label">Kode Booking:</span>
                    <strong><?= $kode_booking ?></strong>
                </div>
                <div class="info-row">
                    <span class="label">Total Pembayaran:</span>
                    <strong class="price">Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?></strong>
                </div>
                <div class="info-row">
                    <span class="label">Metode Pembayaran:</span>
                    <strong><?= strtoupper($payment['metode_bayar'] ?? 'N/A') ?></strong>
                </div>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
    <div class="upload-area" id="uploadArea">
        <input type="file" name="bukti_bayar" id="bukti_bayar" accept="image/*,.pdf" required hidden>
        <label for="bukti_bayar" class="upload-label">
            <div class="upload-icon">
                <i class="bi bi-cloud-upload"></i>
            </div>
            <h4>Klik atau Drag & Drop</h4>
            <p>Format: JPG, PNG, PDF (Max 5MB)</p>
            <span class="upload-btn">Pilih File</span>
        </label>
        <div id="preview" class="preview-area" style="display: none;">
            <img id="previewImage" src="" alt="Preview">
            <button type="button" class="btn-remove-preview" onclick="removePreview()">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    </div>

    <!-- FORM BARU: Nama Pengirim -->
    <div class="form-group">
        <label for="nama_pengirim">Nama Pengirim <span class="required">*</span></label>
        <input type="text" 
               name="nama_pengirim" 
               id="nama_pengirim" 
               class="form-control" 
               placeholder="Nama sesuai rekening bank Anda" 
               required>
        <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 5px;">
            <i class="bi bi-info-circle"></i> Nama yang tertera di rekening pengirim
        </small>
    </div>

    <!-- FORM BARU: Bank Asal -->
    <div class="form-group">
        <label for="bank_asal">Bank Asal Transfer <span class="required">*</span></label>
        <select name="bank_asal" id="bank_asal" class="form-control" required>
            <option value="">-- Pilih Bank / E-wallet --</option>
            <optgroup label="Bank">
                <option value="BCA">BCA</option>
                <option value="BNI">BNI</option>
                <option value="BRI">BRI</option>
                <option value="Mandiri">Mandiri</option>
                <option value="BTN">BTN</option>
                <option value="CIMB Niaga">CIMB Niaga</option>
                <option value="Permata">Permata</option>
                <option value="Danamon">Danamon</option>
                <option value="BSI (Bank Syariah Indonesia)">BSI (Bank Syariah Indonesia)</option>
            </optgroup>
            <optgroup label="E-Wallet">
                <option value="GOPAY">GOPAY</option>
                <option value="OVO">OVO</option>
                <option value="DANA">DANA</option>
                <option value="ShopeePay">ShopeePay</option>
                <option value="LinkAja">LinkAja</option>
            </optgroup>
            <option value="Lainnya">Lainnya</option>
        </select>
        <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 5px;">
            <i class="bi bi-info-circle"></i> Bank/E-wallet yang Anda gunakan untuk transfer
        </small>
    </div>

    <!-- FORM LAMA: Catatan (tetap ada) -->
    <div class="form-group">
        <label for="catatan">Catatan (Opsional)</label>
        <textarea name="catatan" 
                  id="catatan" 
                  rows="3" 
                  class="form-control" 
                  placeholder="Tambahkan catatan jika diperlukan (contoh: transfer pukul 14:30, berita transfer: JawaTrip-JWT123)"></textarea>
        <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 5px;">
            <i class="bi bi-info-circle"></i> Opsional, bisa dikosongkan
        </small>
    </div>

    <div class="upload-instructions">
        <h5><i class="bi bi-info-circle"></i> Panduan Upload:</h5>
        <ul>
            <li>Pastikan bukti pembayaran jelas dan terbaca</li>
            <li>Foto harus menampilkan: nominal, tanggal, dan status berhasil</li>
            <li>Untuk QRIS: screenshot notifikasi pembayaran berhasil</li>
            <li>Untuk VA: foto struk ATM atau screenshot mobile banking</li>
            <li>Ukuran file maksimal 5MB</li>
        </ul>
    </div>

    <div class="action-buttons">
        <a href="pembayaran.php?kode=<?= $kode_booking ?>" class="btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
        <button type="submit" name="submit_bukti" class="btn-primary" id="btnSubmit">
            <i class="bi bi-check-circle"></i>
            Upload & Konfirmasi
        </button>
    </div>
</form>
        </div>
    </div>
</div>

<script>
// File upload preview
const fileInput = document.getElementById('bukti_bayar');
const uploadArea = document.getElementById('uploadArea');
const preview = document.getElementById('preview');
const previewImage = document.getElementById('previewImage');
const btnSubmit = document.getElementById('btnSubmit');

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        handleFile(file);
    }
});

// Drag and drop
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', function() {
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    
    const file = e.dataTransfer.files[0];
    if (file) {
        fileInput.files = e.dataTransfer.files;
        handleFile(file);
    }
});

function handleFile(file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('⚠️ Ukuran file terlalu besar! Maksimal 5MB.');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        alert('⚠️ Format file tidak didukung! Gunakan JPG, PNG, atau PDF.');
        return;
    }
    
    // Show preview
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.style.display = 'block';
            uploadArea.querySelector('.upload-label').style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        previewImage.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="60" fill="%23ef4444"%3EPDF%3C/text%3E%3C/svg%3E';
        preview.style.display = 'block';
        uploadArea.querySelector('.upload-label').style.display = 'none';
    }
    
    btnSubmit.disabled = false;
}

function removePreview() {
    preview.style.display = 'none';
    uploadArea.querySelector('.upload-label').style.display = 'flex';
    fileInput.value = '';
    btnSubmit.disabled = true;
}

// Form validation
document.querySelector('.upload-form').addEventListener('submit', function(e) {
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('⚠️ Silakan pilih file bukti pembayaran terlebih dahulu!');
        return false;
    }
    
    if (!confirm('Apakah Anda yakin data yang diupload sudah benar?')) {
        e.preventDefault();
        return false;
    }
    
    btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengupload...';
    btnSubmit.disabled = true;
});
</script>

<style>
.confirmation-wrapper {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

.confirmation-card-full {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.confirmation-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.confirmation-icon i {
    font-size: 2.5rem;
    color: white;
}

.confirmation-card-full h2 {
    color: #1f2937;
    margin-bottom: 10px;
}

.subtitle {
    color: #6b7280;
    margin-bottom: 30px;
}

.booking-info-box {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row .label {
    color: #6b7280;
}

.info-row .price {
    color: #145C43;
    font-size: 1.2rem;
}

.upload-area {
    position: relative;
    border: 3px dashed #d1d5db;
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 25px;
    transition: all 0.3s;
    background: #f9fafb;
}

.upload-area.drag-over {
    border-color: #145C43;
    background: #f0fdf4;
}

.upload-label {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.upload-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-icon i {
    font-size: 2.5rem;
    color: #0284c7;
}

.upload-label h4 {
    margin: 0;
    color: #1f2937;
}

.upload-label p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.upload-btn {
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    color: white;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(20, 92, 67, 0.3);
}

.preview-area {
    position: relative;
}

.preview-area img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-remove-preview {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ef4444;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.btn-remove-preview:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.upload-instructions {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 20px;
    text-align: left;
    margin-bottom: 25px;
}

.upload-instructions h5 {
    color: #92400e;
    margin-bottom: 15px;
}

.upload-instructions ul {
    margin: 0;
    padding-left: 20px;
    color: #78350f;
}

.upload-instructions li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-secondary, .btn-primary {
    padding: 15px 30px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    font-size: 1rem;
}

.btn-secondary {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(20, 92, 67, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(20, 92, 67, 0.4);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 768px) {
    .confirmation-card-full {
        padding: 25px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-secondary, .btn-primary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>