<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Cek kode booking dari URL atau session
$kode_booking = isset($_GET['kode']) ? $_GET['kode'] : (isset($_SESSION['kode_booking']) ? $_SESSION['kode_booking'] : '');

if (empty($kode_booking)) {
    header("Location: index.php");
    exit;
}

// Query data pemesanan dengan JOIN
$query = "SELECT p.*, pk.nama_paket, pk.gambar_paket, k.nama_kota, pk.durasi,
          (SELECT COUNT(*) FROM penumpang WHERE id_pemesanan = p.id_pemesanan) as total_penumpang
          FROM pemesanan p
          JOIN paket_wisata pk ON p.id_paket = pk.id_paket
          JOIN kota k ON pk.id_kota = k.id_kota
          WHERE p.kode_booking = '$kode_booking'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Pemesanan tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$pemesanan = mysqli_fetch_assoc($result);

// Hitung waktu expired (24 jam dari tanggal pesan)
$waktu_pesan = strtotime($pemesanan['tanggal_pesan']);
$waktu_expired = $waktu_pesan + (24 * 60 * 60);
$sisa_waktu = $waktu_expired - time();

// Cek apakah sudah expired
$is_expired = $sisa_waktu <= 0;

// Format waktu untuk countdown
$jam = floor($sisa_waktu / 3600);
$menit = floor(($sisa_waktu % 3600) / 60);
$detik = $sisa_waktu % 60;
?>

<link rel="stylesheet" href="assets/payment-style.css">

<div class="payment-container">
    <!-- Header -->
    <div class="payment-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="header-text">
                <h1>Selesaikan Pembayaran</h1>
                <p>Kode Booking: <strong><?= $kode_booking ?></strong></p>
            </div>
        </div>
        
        <?php if (!$is_expired): ?>
        <div class="countdown-timer" id="countdownTimer">
            <div class="timer-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="timer-content">
                <p class="timer-label">Selesaikan Pembayaran Dalam:</p>
                <div class="timer-display">
                    <div class="timer-box">
                        <span class="timer-value" id="hours"><?= str_pad($jam, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="timer-unit">Jam</span>
                    </div>
                    <span class="timer-separator">:</span>
                    <div class="timer-box">
                        <span class="timer-value" id="minutes"><?= str_pad($menit, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="timer-unit">Menit</span>
                    </div>
                    <span class="timer-separator">:</span>
                    <div class="timer-box">
                        <span class="timer-value" id="seconds"><?= str_pad($detik, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="timer-unit">Detik</span>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Pembayaran Expired!</strong> Silakan lakukan pemesanan ulang.
        </div>
        <?php endif; ?>
    </div>

    <div class="payment-grid">
        <!-- Ringkasan Pemesanan -->
        <div class="order-summary">
            <div class="summary-card">
                <h3 class="summary-title">
                    <i class="bi bi-receipt"></i>
                    Ringkasan Pemesanan
                </h3>
                
                <div class="package-info">
                    <img src="img/<?= $pemesanan['gambar_paket'] ?>" alt="<?= $pemesanan['nama_paket'] ?>" class="package-image">
                    <div class="package-details">
                        <h4><?= $pemesanan['nama_paket'] ?></h4>
                        <p><i class="bi bi-geo-alt"></i> <?= $pemesanan['nama_kota'] ?></p>
                        <p><i class="bi bi-clock"></i> <?= $pemesanan['durasi'] ?></p>
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-row">
                        <span>Tanggal Keberangkatan</span>
                        <strong><?= date('d M Y', strtotime($pemesanan['tgl_tour'])) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Jumlah Penumpang</span>
                        <strong><?= $pemesanan['jumlah_peserta'] ?> Orang</strong>
                    </div>
                    <div class="detail-row">
                        <span>Dewasa</span>
                        <strong><?= $pemesanan['jumlah_dewasa'] ?> Orang</strong>
                    </div>
                    <div class="detail-row">
                        <span>Anak-anak</span>
                        <strong><?= $pemesanan['jumlah_anak'] ?> Orang</strong>
                    </div>
                </div>

                <div class="price-breakdown">
                    <h4 class="breakdown-title">Rincian Harga</h4>
                    <div class="price-row">
                        <span>Total Pembayaran</span>
                        <strong class="price-value">Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>

            <!-- Info Penting -->
            <div class="info-card">
                <h4><i class="bi bi-info-circle"></i> Informasi Penting</h4>
                <ul>
                    <li>Pembayaran harus diselesaikan dalam 24 jam</li>
                    <li>Konfirmasi otomatis setelah pembayaran terverifikasi</li>
                    <li>E-ticket akan dikirim ke email Anda</li>
                    <li>Simpan bukti pembayaran Anda</li>
                </ul>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="payment-methods">
            <div class="payment-card">
                <h3 class="payment-title">
                    <i class="bi bi-wallet2"></i>
                    Pilih Metode Pembayaran
                </h3>

                <?php if ($is_expired): ?>
                    <div class="alert alert-danger">
                        Pembayaran sudah melewati batas waktu. Silakan lakukan pemesanan ulang.
                    </div>
                <?php else: ?>
                    <!-- QRIS Payment -->
                    <div class="payment-option" onclick="selectPayment('qris')">
                        <div class="option-radio">
                            <input type="radio" name="payment_method" id="qris" value="qris">
                            <label for="qris"></label>
                        </div>
                        <div class="option-content">
                            <img src="assets/img/qris-logo.png" alt="QRIS" class="payment-logo" 
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%2240%22%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%23145C43%22%3EQRIS%3C/text%3E%3C/svg%3E'">
                            <div class="option-info">
                                <h4>QRIS</h4>
                                <p>Scan QR Code dengan aplikasi e-wallet</p>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right option-arrow"></i>
                    </div>

                    <!-- Virtual Account -->
                    <div class="payment-option" onclick="selectPayment('va')">
                        <div class="option-radio">
                            <input type="radio" name="payment_method" id="va" value="va">
                            <label for="va"></label>
                        </div>
                        <div class="option-content">
                            <img src="assets/img/bank-logo.png" alt="Virtual Account" class="payment-logo"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%2240%22%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%23145C43%22%3EBank%3C/text%3E%3C/svg%3E'">
                            <div class="option-info">
                                <h4>Virtual Account</h4>
                                <p>Transfer via ATM, Mobile Banking, Internet Banking</p>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right option-arrow"></i>
                    </div>

                    <!-- Payment Detail Area -->
                    <div id="paymentDetail" class="payment-detail" style="display: none;">
                        <!-- Content will be loaded via AJAX -->
                    </div>

                    <button type="button" class="btn-confirm-payment" id="btnConfirmPayment" style="display: none;">
                        <i class="bi bi-check-circle"></i>
                        Saya Sudah Bayar
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timer
<?php if (!$is_expired): ?>
let timeLeft = <?= $sisa_waktu ?>;

function updateCountdown() {
    if (timeLeft <= 0) {
        document.getElementById('countdownTimer').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Waktu pembayaran telah habis!
            </div>
        `;
        setTimeout(() => {
            alert('Waktu pembayaran telah habis. Halaman akan dimuat ulang.');
            location.reload();
        }, 2000);
        return;
    }

    const hours = Math.floor(timeLeft / 3600);
    const minutes = Math.floor((timeLeft % 3600) / 60);
    const seconds = timeLeft % 60;

    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');

    timeLeft--;
}

setInterval(updateCountdown, 1000);
<?php endif; ?>

// Select Payment Method
function selectPayment(method) {
    document.getElementById(method).checked = true;
    
    // Show loading
    document.getElementById('paymentDetail').style.display = 'block';
    document.getElementById('paymentDetail').innerHTML = `
        <div class="loading-spinner">
            <i class="bi bi-hourglass-split"></i>
            <p>Memuat detail pembayaran...</p>
        </div>
    `;

    // Load payment detail via AJAX
    fetch('payment_processor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=generate&method=${method}&kode_booking=<?= $kode_booking ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (method === 'qris') {
                document.getElementById('paymentDetail').innerHTML = data.html;
            } else {
                document.getElementById('paymentDetail').innerHTML = data.html;
            }
            document.getElementById('btnConfirmPayment').style.display = 'block';
        } else {
            document.getElementById('paymentDetail').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('paymentDetail').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Terjadi kesalahan. Silakan coba lagi.
            </div>
        `;
    });
}

// Confirm Payment
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnConfirmPayment').addEventListener('click', function() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu!');
            return;
        }

        if (confirm('Apakah Anda yakin sudah melakukan pembayaran?')) {
            window.location.href = 'payment_confirmation.php?kode=<?= $kode_booking ?>';
        }
    });
});

// Copy function
function copyText(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Tersalin';
        button.style.background = '#10b981';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '';
        }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>