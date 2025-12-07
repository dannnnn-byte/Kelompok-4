<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Cek apakah sudah ada data booking dan penumpang di session
if (!isset($_SESSION['booking']) || !isset($_SESSION['penumpang'])) {
    header("Location: pesan.php");
    exit;
}

$id_paket = $_GET['id_paket'];
$booking = $_SESSION['booking'];
$penumpang = $_SESSION['penumpang'];

// Query data paket
$query_paket = "SELECT p.*, k.nama_kota 
                FROM paket_wisata p 
                JOIN kota k ON p.id_kota = k.id_kota 
                WHERE p.id_paket = '$id_paket'";
$result_paket = mysqli_query($conn, $query_paket);
$paket = mysqli_fetch_assoc($result_paket);

// Fungsi hitung total
function hitungTotal($dewasa, $anak, $harga) {
    $total_dewasa = $dewasa * $harga;
    $total_anak = $anak * ($harga * 0.7);
    return $total_dewasa + $total_anak;
}

$total = hitungTotal($booking['dewasa'], $booking['anak'], $paket['harga_per_pax']);

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_pembayaran'])) {
    // Generate kode booking unik
    $kode_booking = 'JWT' . date('Ymd') . strtoupper(substr(md5(time()), 0, 4));
    
    // ID Pemesanan format: PMS + timestamp
    $id_pemesanan = 'PMS' . time();
    
    $tanggal_pesan = date('Y-m-d H:i:s');
    $status = 'pending';
    $total_peserta = $booking['dewasa'] + $booking['anak'];
    
    // Mulai transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert data pemesanan sesuai struktur database
        $query_insert = "INSERT INTO pemesanan (
            id_pemesanan, 
            kode_booking,
            id_user, 
            id_paket, 
            tgl_tour,
            tanggal_keberangkatan, 
            jumlah_peserta,
            jumlah_dewasa, 
            jumlah_anak, 
            total_bayar,
            total_harga, 
            status_bayar,
            status, 
            tanggal_pesan
        ) VALUES (
            '$id_pemesanan',
            '$kode_booking',
            NULL,
            '$id_paket', 
            '{$booking['tanggal']}',
            '{$booking['tanggal']}', 
            $total_peserta,
            {$booking['dewasa']}, 
            {$booking['anak']}, 
            $total,
            $total, 
            'pending',
            '$status', 
            '$tanggal_pesan'
        )";
        
        if (!mysqli_query($conn, $query_insert)) {
            throw new Exception("Error insert pemesanan: " . mysqli_error($conn));
        }
        
        // Insert data penumpang
        foreach ($penumpang as $p) {
            // Escape input untuk keamanan
            $nama = mysqli_real_escape_string($conn, $p['nama']);
            $email = mysqli_real_escape_string($conn, $p['email']);
            $telepon = mysqli_real_escape_string($conn, $p['telepon']);
            $alamat = mysqli_real_escape_string($conn, $p['alamat']);
            $identitas = mysqli_real_escape_string($conn, $p['identitas']);
            $tipe = $p['tipe'];
            
            $query_penumpang = "INSERT INTO penumpang (
                id_pemesanan, 
                nama_lengkap, 
                email, 
                no_telepon, 
                alamat, 
                no_identitas, 
                tipe_penumpang
            ) VALUES (
                '$id_pemesanan', 
                '$nama', 
                '$email', 
                '$telepon', 
                '$alamat', 
                '$identitas', 
                '$tipe'
            )";
            
            if (!mysqli_query($conn, $query_penumpang)) {
                throw new Exception("Error insert penumpang: " . mysqli_error($conn));
            }
        }
        
        // Insert log aktivitas
        $query_log = "INSERT INTO booking_log (id_pemesanan, aktivitas, keterangan) 
                     VALUES ('$id_pemesanan', 'Pemesanan Dibuat', 'Booking baru dengan kode: $kode_booking')";
        mysqli_query($conn, $query_log);
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Simpan info ke session
        $_SESSION['kode_booking'] = $kode_booking;
        $_SESSION['id_pemesanan'] = $id_pemesanan;
        $_SESSION['success_message'] = "Pemesanan berhasil dibuat dengan kode: $kode_booking";
        
        // Hapus data temporary dari session
        unset($_SESSION['booking']);
        unset($_SESSION['penumpang']);
        
        // Redirect ke halaman pembayaran
        header("Location: pembayaran.php?kode=$kode_booking");
        exit;
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        $error_message = "Terjadi kesalahan: " . $e->getMessage();
        error_log($error_message);
    }
}
?>

<link rel="stylesheet" href="assets/booking-style.css">

<div class="booking-container">
    <div class="booking-header">
        <h1 class="booking-title">Booking Wisata</h1>
        <p class="booking-subtitle">Lengkapi data pemesanan Anda dengan mudah</p>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-step completed">
            <div class="step-circle">
                <i class="bi bi-check-lg"></i>
            </div>
            <span class="step-label">Detail Pemesanan</span>
        </div>
        <div class="progress-line active"></div>
        <div class="progress-step completed">
            <div class="step-circle">
                <i class="bi bi-check-lg"></i>
            </div>
            <span class="step-label">Data Penumpang</span>
        </div>
        <div class="progress-line active"></div>
        <div class="progress-step active">
            <div class="step-circle">3</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <!-- STEP 3: KONFIRMASI -->
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <div class="success-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2 class="confirmation-title">Konfirmasi Pemesanan</h2>
                <p class="confirmation-subtitle">Periksa kembali detail pemesanan Anda</p>
            </div>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background: #fee; border-radius: 10px; color: #c00;">
                <i class="bi bi-exclamation-triangle"></i> <?= $error_message ?>
            </div>
            <?php endif; ?>

            <!-- Detail Paket -->
            <div class="detail-section">
                <h3 class="section-title">
                    <i class="bi bi-map"></i> Detail Paket Wisata
                </h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <p class="detail-label">Nama Paket</p>
                        <p class="detail-value"><?= $paket['nama_paket'] ?></p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Lokasi</p>
                        <p class="detail-value"><?= $paket['nama_kota'] ?></p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Tanggal Keberangkatan</p>
                        <p class="detail-value">
                            <?php 
                            $tanggal = new DateTime($booking['tanggal']);
                            setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian');
                            echo strftime('%d %B %Y', $tanggal->getTimestamp());
                            ?>
                        </p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Durasi</p>
                        <p class="detail-value"><?= $paket['durasi'] ?></p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Jumlah Penumpang</p>
                        <p class="detail-value"><?= $booking['dewasa'] ?> Dewasa, <?= $booking['anak'] ?> Anak</p>
                    </div>
                    <div class="detail-item">
                        <p class="detail-label">Total Penumpang</p>
                        <p class="detail-value"><?= $booking['total_penumpang'] ?> Orang</p>
                    </div>
                </div>
            </div>

            <!-- Daftar Penumpang -->
            <div class="detail-section">
                <h3 class="section-title">
                    <i class="bi bi-people"></i> Daftar Penumpang
                </h3>
                <div class="passenger-list-final">
                    <?php $no = 1; foreach ($penumpang as $p): ?>
                    <div class="passenger-card-final">
                        <div class="passenger-info">
                            <p class="passenger-name">
                                <i class="bi bi-person-circle"></i> 
                                <?= $no ?>. <?= htmlspecialchars($p['nama']) ?>
                            </p>
                            <p class="passenger-details">
                                <span><i class="bi bi-envelope"></i> <?= htmlspecialchars($p['email']) ?></span> | 
                                <span><i class="bi bi-phone"></i> <?= htmlspecialchars($p['telepon']) ?></span>
                            </p>
                            <p class="passenger-details">
                                <i class="bi bi-house"></i> <?= htmlspecialchars($p['alamat']) ?>
                            </p>
                            <p class="passenger-details">
                                <i class="bi bi-card-text"></i> <?= htmlspecialchars($p['identitas']) ?>
                            </p>
                        </div>
                        <span class="passenger-badge"><?= $p['tipe'] ?></span>
                    </div>
                    <?php $no++; endforeach; ?>
                </div>
            </div>

            <!-- Rincian Biaya -->
            <div class="detail-section" style="background: #f0f9ff;">
                <h3 class="section-title">
                    <i class="bi bi-calculator"></i> Rincian Biaya
                </h3>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px 0;">
                        <span><?= $booking['dewasa'] ?> Penumpang Dewasa × Rp <?= number_format($paket['harga_per_pax'], 0, ',', '.') ?></span>
                        <span style="font-weight: 600;">Rp <?= number_format($booking['dewasa'] * $paket['harga_per_pax'], 0, ',', '.') ?></span>
                    </div>
                    <?php if ($booking['anak'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px 0; border-top: 1px solid #e5e7eb;">
                        <span><?= $booking['anak'] ?> Penumpang Anak × Rp <?= number_format($paket['harga_per_pax'] * 0.7, 0, ',', '.') ?> <small>(Diskon 30%)</small></span>
                        <span style="font-weight: 600;">Rp <?= number_format($booking['anak'] * ($paket['harga_per_pax'] * 0.7), 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Total Pembayaran -->
            <div class="payment-summary">
                <div class="payment-info">
                    <p class="payment-label">
                        <i class="bi bi-cash-stack"></i> Total Pembayaran
                    </p>
                    <p class="payment-amount">Rp <?= number_format($total, 0, ',', '.') ?></p>
                    <p class="payment-label" style="font-size: 0.85rem; margin-top: 5px;">
                        Untuk <?= $booking['total_penumpang'] ?> Penumpang
                    </p>
                </div>
                <i class="bi bi-credit-card payment-icon"></i>
            </div>

            <!-- Informasi Penting -->
            <div class="detail-section" style="background: #fff7ed; border-left: 4px solid #f59e0b;">
                <h3 class="section-title" style="color: #f59e0b;">
                    <i class="bi bi-info-circle"></i> Informasi Penting
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px; display: flex; align-items: start;">
                        <i class="bi bi-check-circle-fill" style="color: #10b981; margin-right: 10px; margin-top: 3px;"></i>
                        <span>Pastikan semua data yang Anda masukkan sudah benar</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; align-items: start;">
                        <i class="bi bi-check-circle-fill" style="color: #10b981; margin-right: 10px; margin-top: 3px;"></i>
                        <span>Anda akan menerima email konfirmasi setelah pembayaran berhasil</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; align-items: start;">
                        <i class="bi bi-check-circle-fill" style="color: #10b981; margin-right: 10px; margin-top: 3px;"></i>
                        <span>Silakan lakukan pembayaran dalam 24 jam untuk mengonfirmasi pemesanan</span>
                    </li>
                    <li style="display: flex; align-items: start;">
                        <i class="bi bi-check-circle-fill" style="color: #10b981; margin-right: 10px; margin-top: 3px;"></i>
                        <span>Hubungi customer service jika ada pertanyaan: <strong>0812-3456-7890</strong></span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <form method="POST" class="confirmation-actions">
                <a href="pesan_step2.php?id_paket=<?= $id_paket ?>" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Data Penumpang
                </a>
                <button type="submit" name="submit_pembayaran" class="btn-primary" 
                        onclick="return confirm('Apakah Anda yakin semua data sudah benar?')">
                    <i class="bi bi-credit-card"></i>
                    Konfirmasi & Lanjut ke Pembayaran
                </button>
            </form>

            <!-- Note -->
            <p style="text-align: center; color: #6b7280; font-size: 0.85rem; margin-top: 20px;">
                <i class="bi bi-shield-check"></i> 
                Data Anda aman dan terlindungi
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>