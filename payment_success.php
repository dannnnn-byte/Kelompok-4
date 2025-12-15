<?php
session_start();
include 'koneksi.php';

// Debug sementara (aktifkan saat development)
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ambil kode booking (escape)
$kode_booking = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';

if (empty($kode_booking)) {
    header("Location: index.php");
    exit;
}

// --- Ambil data pemesanan ---
$query = "SELECT p.*, pk.nama_paket, pk.gambar_paket 
          FROM pemesanan p
          JOIN paket_wisata pk ON p.id_paket = pk.id_paket
          WHERE p.kode_booking = '$kode_booking'";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    // jika tidak ditemukan, redirect kembali
    header("Location: index.php");
    exit;
}
$pemesanan = mysqli_fetch_assoc($result);

// ===== mengambil kolom tanggal yang tersedia di tabel pembayaran (robust) =====
$kode_booking_esc = $kode_booking;
$cols = [];
$res_cols = mysqli_query($conn, "SHOW COLUMNS FROM pembayaran");
if ($res_cols) {
    while ($r = mysqli_fetch_assoc($res_cols)) {
        $cols[] = $r['Field'];
    }
}
$possible = ['tgl_bayar','tanggal_bayar','tanggal_konfirmasi','tgl','created_at','tanggal_pesan'];
$dateCol = null;
foreach ($possible as $c) {
    if (in_array($c, $cols)) {
        $dateCol = $c;
        break;
    }
}
$orderBy = $dateCol ? "ORDER BY `$dateCol` DESC" : "";
// debug comment (bisa dilihat di view-source)
echo "<!-- Debug: pembayaran cols: " . htmlspecialchars(implode(',', $cols)) . " | using dateCol: " . htmlspecialchars($dateCol) . " -->";

$query_payment = "SELECT * FROM pembayaran WHERE kode_booking = '$kode_booking_esc' $orderBy LIMIT 1";
$result_payment = mysqli_query($conn, $query_payment);
$payment = $result_payment && mysqli_num_rows($result_payment) ? mysqli_fetch_assoc($result_payment) : null;
// ===== end payment lookup =====

// --- Proses form upload (HARUS sebelum include header/navbar sehingga header() bekerja) ---
$error = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_bukti'])) {
    $upload_dir = __DIR__ . '/uploads/bukti_bayar/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $error = "Gagal membuat folder upload. Cek permission.";
        }
    }
    
    if (!$error) {
        if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
            $file = $_FILES['bukti_bayar'];
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $file['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'bukti_' . $kode_booking . '_' . time() . '.' . $ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Escape input sebelum simpan ke DB
                    $catatan = isset($_POST['catatan']) ? mysqli_real_escape_string($conn, $_POST['catatan']) : '';
                    $nama_pengirim = isset($_POST['nama_pengirim']) ? mysqli_real_escape_string($conn, $_POST['nama_pengirim']) : '';
                    $bank_asal = isset($_POST['bank_asal']) ? mysqli_real_escape_string($conn, $_POST['bank_asal']) : '';
                    $new_filename_db = mysqli_real_escape_string($conn, $new_filename);

                    // Gunakan id_pembayaran jika tersedia agar UPDATE lebih spesifik
                    if ($payment && !empty($payment['id_pembayaran'])) {
                        $id_pembayaran_update = mysqli_real_escape_string($conn, $payment['id_pembayaran']);
                        $where = "id_pembayaran = '$id_pembayaran_update'";
                    } else {
                        // fallback: update berdasarkan kode_booking (hati-hati jika ada banyak row)
                        $where = "kode_booking = '$kode_booking_esc'";
                    }

                    $query_update = "UPDATE pembayaran SET 
                        bukti_bayar = '$new_filename_db',
                        catatan = '$catatan',
                        status_bayar = 'menunggu_verifikasi',
                        tanggal_konfirmasi = NOW(),
                        nama_pengirim = '$nama_pengirim',
                        bank_asal = '$bank_asal'
                        WHERE $where";

                    if (mysqli_query($conn, $query_update)) {
                        // Update status pemesanan
                        mysqli_query($conn, "UPDATE pemesanan SET status = 'menunggu_verifikasi' WHERE kode_booking = '$kode_booking_esc'");
                        
                        // Insert log (escape id_pemesanan)
                        $id_pemesanan_log = mysqli_real_escape_string($conn, $pemesanan['id_pemesanan']);
                        mysqli_query($conn, "INSERT INTO booking_log (id_pemesanan, aktivitas, keterangan) 
                                            VALUES ('$id_pemesanan_log', 'Bukti Bayar Diupload', 'Menunggu verifikasi admin')");
                        
                        $_SESSION['success_message'] = "Bukti pembayaran berhasil diupload! Mohon tunggu verifikasi dari admin.";
                        header("Location: payment_success.php?kode=" . urlencode($kode_booking_esc));
                        exit;
                    } else {
                        $error = "Gagal menyimpan data ke database: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Gagal mengupload file. Pastikan folder uploads dapat ditulis oleh webserver.";
                }
            } else {
                $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau PDF.";
            }
        } else {
            $error = "Silakan pilih file bukti pembayaran.";
        }
    }
}

// Jika sampai sini, proses selesai / belum submit atau ada error => lanjut render halaman
include 'includes/header.php';
include 'includes/navbar.php';
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
                    <strong><?= htmlspecialchars($kode_booking) ?></strong>
                </div>
                <div class="info-row">
                    <span class="label">Total Pembayaran:</span>
                    <strong class="price">Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?></strong>
                </div>
                <div class="info-row">
                    <span class="label">Metode Pembayaran:</span>
                    <strong><?= strtoupper(htmlspecialchars($payment['metode_bayar'] ?? 'N/A')) ?></strong>
                </div>
            </div>

            <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
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

                <div class="form-group">
                    <label for="nama_pengirim">Nama Pengirim <span class="required">*</span></label>
                    <input type="text" name="nama_pengirim" id="nama_pengirim" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="bank_asal">Bank Asal Transfer <span class="required">*</span></label>
                    <select name="bank_asal" id="bank_asal" class="form-control" required>
                        <option value="">-- Pilih Bank / E-wallet --</option>
                        <optgroup label="Bank">
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Mandiri">Mandiri</option>
                        </optgroup>
                        <optgroup label="E-Wallet">
                            <option value="GOPAY">GOPAY</option>
                            <option value="OVO">OVO</option>
                        </optgroup>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="catatan">Catatan (Opsional)</label>
                    <textarea name="catatan" id="catatan" rows="3" class="form-control"></textarea>
                </div>

                <div class="action-buttons">
                    <a href="pembayaran.php?kode=<?= htmlspecialchars($kode_booking) ?>" class="btn-secondary">
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
// (JS preview yang sama — tidak berubah)
const fileInput = document.getElementById('bukti_bayar');
// ... tetapkan semua JS yang Anda punya (untuk ringkas saya tidak ulang semuanya di sini)
</script>

<?php include 'includes/footer.php'; ?>
<style>
.success-wrapper {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.success-card {
    background: white;
    border-radius: 20px;
    padding: 50px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.success-animation {
    margin-bottom: 30px;
}

.checkmark-circle {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #10b981;
    stroke-miterlimit: 10;
    animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
}

.checkmark-circle-path {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #10b981;
    fill: #f0fdf4;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    stroke: #10b981;
    stroke-width: 3;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0 0 0 50px #10b981;
    }
}

.success-title {
    color: #1f2937;
    font-size: 2rem;
    margin-bottom: 10px;
}

.success-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
    margin-bottom: 40px;
}

.booking-card-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    border: 2px solid #10b981;
}

.booking-header-success {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #d1fae5;
    text-align: left;
}

.booking-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
}

.booking-info h3 {
    margin: 0 0 5px 0;
    color: #1f2937;
}

.booking-info p {
    margin: 0;
    color: #6b7280;
}

.booking-details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 15px;
    text-align: left;
    background: white;
    padding: 15px;
    border-radius: 10px;
}

.detail-item i {
    font-size: 1.5rem;
    color: #10b981;
}

.detail-item .label {
    color: #6b7280;
    font-size: 0.85rem;
    margin: 0 0 5px 0;
}

.detail-item .value {
    color: #1f2937;
    font-weight: 700;
    margin: 0;
}

.detail-item .value.price {
    color: #145C43;
    font-size: 1.2rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
}

.status-badge.menunggu_verifikasi {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.lunas {
    background: #d1fae5;
    color: #065f46;
}

.passenger-list-success {
    background: #f9fafb;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: left;
}

.passenger-list-success h4 {
    margin: 0 0 20px 0;
    color: #1f2937;
}

.passenger-item-success {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    margin-bottom: 10px;
}

.passenger-number {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.passenger-details {
    flex: 1;
}

.passenger-details h5 {
    margin: 0 0 5px 0;
    color: #1f2937;
}

.passenger-details p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.passenger-type-badge {
    background: #e0f2fe;
    color: #0369a1;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.action-buttons-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.btn-download, .btn-home {
    padding: 15px 30px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}

.btn-download {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.btn-home {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-home:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.next-steps {
    background: #eff6ff;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    text-align: left;
    border-left: 4px solid #3b82f6;
}

.next-steps h4 {
    margin: 0 0 20px 0;
    color: #1e40af;
}

.steps-grid {
    display: grid;
    gap: 15px;
}

.step-item {
    display: flex;
    gap: 15px;
    align-items: start;
}

.step-number {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.step-content h5 {
    margin: 0 0 5px 0;
    color: #1f2937;
}

.step-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.5;
}

.contact-info {
    background: #f9fafb;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
}

.contact-info p {
    margin: 0 0 10px 0;
    color: #4b5563;
}

.contact-methods {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 15px;
}

.contact-btn {
    background: white;
    color: #145C43;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s;
}

.contact-btn:hover {
    border-color: #145C43;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(20, 92, 67, 0.2);
}

@media (max-width: 768px) {
    .success-card {
        padding: 30px 20px;
    }
    
    .booking-details-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-methods {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>