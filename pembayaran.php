<?php
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

$kode = $_GET['kode'] ?? '';

$query = "SELECT p.*, pw.nama_paket 
          FROM pemesanan p 
          JOIN paket_wisata pw ON p.id_paket = pw.id_paket
          WHERE p.kode_booking = '$kode'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="container py-5 text-center">
    <h2 class="fw-bold">Pembayaran</h2>
    <p>Kode Booking</p>
    <h3 class="text-success fw-bold"><?= $data['kode_booking']; ?></h3>

    <p>Total Pembayaran</p>
    <h2 class="fw-bold">Rp <?= number_format($data['total_bayar'],0,',','.'); ?></h2>

    <div class="alert alert-warning mt-4">
        Silakan lakukan pembayaran ke rekening berikut:
        <br><strong>BRI 123456789 a.n JawaTrip</strong>
    </div>

    <p>Status: <span class="badge bg-warning"><?= $data['status_bayar']; ?></span></p>
</div>

<?php include 'includes/footer.php'; ?>
