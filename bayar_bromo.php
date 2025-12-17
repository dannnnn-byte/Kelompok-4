<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';
 

// ================= AMBIL PEMESANAN TERAKHIR USER =================
$query = "SELECT pb.*, u.nama_lengkap 
          FROM pemesanan_bromo pb
          LEFT JOIN users u ON pb.user_id = u.id_user
          ORDER BY pb.id DESC LIMIT 1";

$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) == 0){
    echo "<script>alert('Belum ada pemesanan Bromo.'); window.location='index.php';</script>";
    exit;
}

$pemesanan = mysqli_fetch_assoc($result);

// Pastikan timezone server sesuai
date_default_timezone_set('Asia/Jakarta');

// Ambil waktu pesan dari database
$created_at = $pemesanan['created_at']; // format DATETIME misal "2025-12-16 14:00:00"

// Buat objek DateTime untuk waktu pesan
$waktu_pesan = new DateTime($created_at, new DateTimeZone('Asia/Jakarta'));

// Tambahkan 24 jam untuk expired
$waktu_expired = clone $waktu_pesan;
$waktu_expired->modify('+24 hours');

// Ambil waktu sekarang
$now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));

// Hitung selisih dalam detik
$sisa_waktu = $waktu_expired->getTimestamp() - $now->getTimestamp();

// Jika waktu sudah habis
$is_expired = $sisa_waktu <= 0;

// Hitung jam, menit, detik
$jam = max(floor($sisa_waktu / 3600), 0);
$menit = max(floor(($sisa_waktu % 3600) / 60), 0);
$detik = max($sisa_waktu % 60, 0);


// ================= CEK BUKTI PEMBAYARAN =================
$query_bayar = "SELECT * FROM pembayaran_bromo WHERE bromo_id={$pemesanan['id']}";
$res_bayar = mysqli_query($conn, $query_bayar);
$bukti = mysqli_fetch_assoc($res_bayar);
$sudah_upload = !empty($bukti['bukti_bayar']);

// ================= QRIS =================
$qr_text  = "PEMBAYARAN|BROMO|ID:{$pemesanan['id']}|TOTAL:{$pemesanan['total_harga']}";
$qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . urlencode($qr_text);


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
            </div>
        </div>

        <?php if(!$is_expired): ?>
        <div class="countdown-timer" id="countdownTimer">
            <div class="timer-icon"><i class="bi bi-clock-history"></i></div>
            <div class="timer-content">
                <p class="timer-label">Selesaikan pembayaran dalam:</p>
                <div class="timer-display">
                    <div class="timer-box"><span class="timer-value" id="hours"><?= str_pad($jam,2,'0',STR_PAD_LEFT) ?></span><span class="timer-unit">Jam</span></div>
                    <span class="timer-separator">:</span>
                    <div class="timer-box"><span class="timer-value" id="minutes"><?= str_pad($menit,2,'0',STR_PAD_LEFT) ?></span><span class="timer-unit">Menit</span></div>
                    <span class="timer-separator">:</span>
                    <div class="timer-box"><span class="timer-value" id="seconds"><?= str_pad($detik,2,'0',STR_PAD_LEFT) ?></span><span class="timer-unit">Detik</span></div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Pembayaran Expired! Silakan lakukan pemesanan ulang.
        </div>
        <?php endif; ?>
    </div>

    <div class="payment-grid">
        <!-- Ringkasan Pemesanan Bromo -->
        <div class="order-summary">
            <div class="summary-card">
                <h3 class="summary-title"><i class="bi bi-receipt"></i> Ringkasan Pemesanan</h3>
                <div class="package-info">
                    <h4>Gunung Bromo Tour</h4>
                    <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($pemesanan['nama_lengkap']) ?></p>
                    <p><strong>Jumlah Orang:</strong> <?= $pemesanan['jumlah_orang'] ?> orang</p>
                    <p><strong>Jeep:</strong> <?= $pemesanan['sewa_jeep']=='ya'?'Ya':'-' ?></p>
                    <p><strong>Trail:</strong> <?= $pemesanan['sewa_trail']=='ya'? $pemesanan['jumlah_trail'].' unit':'-' ?></p>
                </div>
                <div class="price-breakdown">
                    <h4>Rincian Harga</h4>
                    <div class="price-row">
                        <span>Total Pembayaran</span>
                        <strong>Rp <?= number_format($pemesanan['total_harga'],0,',','.') ?></strong>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h4><i class="bi bi-info-circle"></i> Informasi Penting</h4>
                <ul>
                    <li>Pembayaran harus diselesaikan dalam 24 jam</li>
                    <li>Konfirmasi otomatis setelah pembayaran terverifikasi</li>
                    <li>Simpan bukti pembayaran Anda</li>
                </ul>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="payment-methods">
            <div class="payment-card">
                <h3 class="payment-title"><i class="bi bi-wallet2"></i> Pilih Metode Pembayaran</h3>
                <?php if($is_expired): ?>
                    <div class="alert alert-danger">Pembayaran sudah melewati batas waktu.</div>
                <?php else: ?>
                    <!-- QRIS -->
                 <div class="payment-option" onclick="selectPayment('qris')">
    <div class="option-radio">
        <input type="radio" name="payment_method" id="qris" value="qris">
        <label for="qris"></label>
    </div>

    <div class="option-content">
        <img src="<?= $qr_image ?>" 

             alt="QR Code Pembayaran"
             class="qr-code-image">

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

        <div class="option-info">
            <h4>Virtual Account</h4>
            <p>Transfer via ATM / Mobile / Internet Banking</p>
        </div>
    </div>

    <i class="bi bi-chevron-right option-arrow"></i>
</div>

<!-- DETAIL PEMBAYARAN -->
<div id="paymentDetail" class="payment-detail" style="display:none;"></div>


                

                    <!-- Upload Bukti (Opsional) -->
                    <?php if(!$sudah_upload): ?>
                    <form action="upload_bukti_bromo.php" method="POST" enctype="multipart/form-data" class="mt-3">
                        <input type="hidden" name="bromo_id" value="<?= $pemesanan['id'] ?>">
                        <input type="file" name="bukti_bayar" accept="image/*" required>
                        <button type="submit" class="btn btn-success w-100">Upload Bukti</button>
                    </form>
                    <?php else: ?>
                    <p class="text-success">Bukti pembayaran sudah diunggah.</p>
                    <?php endif; ?>


                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timer
<?php if(!$is_expired): ?>
let timeLeft = <?= $sisa_waktu ?>;
function updateCountdown(){
    if(timeLeft<=0){
        document.getElementById('countdownTimer').innerHTML = '<div class="alert alert-danger">Waktu pembayaran habis!</div>';
        setTimeout(()=>location.reload(),2000);
        return;
    }
    const h=Math.floor(timeLeft/3600);
    const m=Math.floor((timeLeft%3600)/60);
    const s=timeLeft%60;
    document.getElementById('hours').textContent=String(h).padStart(2,'0');
    document.getElementById('minutes').textContent=String(m).padStart(2,'0');
    document.getElementById('seconds').textContent=String(s).padStart(2,'0');
    timeLeft--;
}
setInterval(updateCountdown,1000);
<?php endif; ?>

// Select Payment Method
function selectPayment(method){
    document.getElementById(method).checked=true;
    document.getElementById('paymentDetail').style.display='block';
    document.getElementById('paymentDetail').innerHTML='<p>Memuat detail pembayaran...</p>';
    document.getElementById('btnConfirmPayment').style.display='block';
}

// Confirm Payment
document.getElementById('btnConfirmPayment').addEventListener('click', function(){
    if(confirm('Apakah Anda yakin sudah melakukan pembayaran?')){
        window.location.href='payment_confirmation.php?id=<?= $pemesanan['id'] ?>';
    }
});

function selectPayment(method) {
    document.getElementById(method).checked = true;

    const detail = document.getElementById('paymentDetail');
    detail.style.display = 'block';

    if (method === 'va') {
        detail.innerHTML = `
            <div class="alert alert-info mt-3">
                <h5>Instruksi Virtual Account</h5>
                <ol>
                    <li>Pilih salah satu bank di bawah</li>
                    <li>Salin nomor Virtual Account</li>
                    <li>Lakukan transfer sesuai total pembayaran</li>
                </ol>

                <div class="va-box">
                    <strong>BANK BCA</strong><br>
                    <span class="va-number">1234 5678 9012 3456</span>
                </div>

                <div class="va-box">
                    <strong>BANK BRI</strong><br>
                    <span class="va-number">8888 9999 0000 1111</span>
                </div>

                <div class="va-box">
                    <strong>BANK MANDIRI</strong><br>
                    <span class="va-number">7001 2345 6789</span>
                </div>
            </div>
        `;
    }

    if (method === 'qris') {
        detail.innerHTML = `
            <div class="alert alert-success mt-3">
                Silakan scan QR Code di atas menggunakan e-wallet Anda.
            </div>
        `;
    }
}

</script>

<?php include 'includes/footer.php'; ?>
