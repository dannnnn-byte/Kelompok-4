<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

/* ================= VALIDASI ID ================= */
$id_pemesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pemesanan <= 0) {
    header("Location: bromo.php");
    exit;
}

/* ================= AMBIL DATA PEMESANAN BROMO ================= */
$query = "SELECT pb.*, u.nama_lengkap
          FROM pemesanan_bromo pb
          LEFT JOIN users u ON pb.user_id = u.id_user
          WHERE pb.id = $id_pemesanan";
$result = mysqli_query($conn, $query);
$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    header("Location: bromo.php");
    exit;
}

/* ================= AMBIL DATA PEMBAYARAN ================= */
$query_payment = "SELECT *
                  FROM pembayaran_bromo
                  WHERE bromo_id = $id_pemesanan
                  ORDER BY created_at DESC
                  LIMIT 1";
$result_payment = mysqli_query($conn, $query_payment);
$payment = mysqli_fetch_assoc($result_payment);

/* ================= DATA PESERTA (DUMMY SESUAI STRUKTUR) ================= */
$jumlah = $pemesanan['jumlah_orang'];
?>

<link rel="stylesheet" href="assets/payment-style.css">

<div class="payment-container">
    <div class="success-wrapper">
        <div class="success-card">

            <!-- Success Animation -->
            <div class="success-animation">
                <div class="checkmark-circle">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle-path" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>

            <h1 class="success-title">Pembayaran Berhasil Diupload!</h1>
            <p class="success-subtitle">Terima kasih atas pembayaran Anda</p>

            <!-- Booking Card -->
            <div class="booking-card-success">
                <div class="booking-header-success">
                    <div class="booking-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="booking-info">
                        <h3>Gunung Bromo Tour</h3>
                        <p>
                            <i class="bi bi-calendar"></i>
                            <?= date('d M Y', strtotime($pemesanan['created_at'])) ?>
                        </p>
                    </div>
                </div>

                <div class="booking-details-grid">
                    <div class="detail-item">
                        <i class="bi bi-receipt"></i>
                        <div>
                            <p class="label">ID Pemesanan</p>
                            <p class="value">#<?= $pemesanan['id'] ?></p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-cash-stack"></i>
                        <div>
                            <p class="label">Total Pembayaran</p>
                            <p class="value price">
                                Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?>
                            </p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-people"></i>
                        <div>
                            <p class="label">Jumlah Peserta</p>
                            <p class="value"><?= $pemesanan['jumlah_orang'] ?> Orang</p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-hourglass-split"></i>
                        <div>
                            <p class="label">Status</p>
                            <?php
                                $status = strtolower($payment['status_bayar'] ?? 'pending');

                                if ($status === 'lunas') {
                                    $badgeClass = 'lunas';
                                    $iconClass  = 'bi bi-check-circle';
                                    $statusText = 'Lunas';
                                } elseif ($status === 'pending') {
                                    $badgeClass = 'pending';
                                    $iconClass  = 'bi bi-clock-history';
                                    $statusText = 'pending';
                                } else {
                                    $badgeClass = 'pending';
                                    $iconClass  = 'bi bi-clock-history';
                                    $statusText = 'Pending';
                                }
                            ?>
                            <span class="status-badge <?= $badgeClass ?>">
                                <i class="<?= $iconClass ?>"></i> <?= $statusText ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Peserta -->
            <div class="passenger-list-success">
                <h4><i class="bi bi-people"></i> Daftar Peserta</h4>

                <?php for ($i = 1; $i <= $jumlah; $i++): ?>
                <div class="passenger-item-success">
                    <div class="passenger-number"><?= $i ?></div>
                    <div class="passenger-details">
                        <h5><?= htmlspecialchars($pemesanan['nama_lengkap']) ?></h5>
                        <p>-</p>
                    </div>
                    <span class="passenger-type-badge">Peserta</span>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons-grid">
                <a href="riwayat_bromo.php" class="btn-home">
                    <i class="bi bi-house"></i> Riwayat Pemesanan
                </a>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h4><i class="bi bi-list-check"></i> Langkah Selanjutnya</h4>
                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h5>Verifikasi Pembayaran</h5>
                            <p>Tim kami akan memverifikasi pembayaran Anda dalam 1x24 jam</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h5>Konfirmasi</h5>
                            <p>Anda akan menerima konfirmasi setelah pembayaran diverifikasi</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h5>Persiapan Trip</h5>
                            <p>Detail perjalanan akan dikirim sebelum keberangkatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="contact-info">
                <p><i class="bi bi-headset"></i> <strong>Butuh Bantuan?</strong></p>
                <div class="contact-methods">
                    <a href="https://wa.me/6281234567890" class="contact-btn">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="mailto:support@jawatrip.com" class="contact-btn">
                        <i class="bi bi-envelope"></i> Email
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Tambahkan CSS dari file lama yang sudah ada styling success page */
</style>



<script>
// (JS preview yang sama — tidak berubah)
const fileInput = document.getElementById('bukti_bayar');
// ... tetapkan semua JS yang Anda punya (untuk ringkas saya tidak ulang semuanya di sini)
</script>

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

.status-badge.pending {
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
