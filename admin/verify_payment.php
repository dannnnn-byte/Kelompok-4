<?php
require_once '../includes/auth.php';
onlyAdmin();
require_once '../koneksi.php';

// Ensure JSON or redirect? We'll do a simple POST handler with redirects
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$kode_booking = isset($_POST['kode_booking']) ? mysqli_real_escape_string($conn, $_POST['kode_booking']) : '';

if ($kode_booking === '') {
    $_SESSION['error_message'] = 'Kode booking tidak valid.';
    header('Location: dashboard.php');
    exit;
}

// Verify: mark pembayaran as lunas and set tanggal_konfirmasi
// Also update pemesanan status_bayar to lunas and total_bayar to total_harga if empty
mysqli_begin_transaction($conn);
try {
    // Update pembayaran: set menunggu_verifikasi/pending -> lunas
    $q1 = "UPDATE pembayaran SET status_bayar = 'lunas', tanggal_konfirmasi = NOW() WHERE kode_booking = '$kode_booking' AND status_bayar IN ('menunggu_verifikasi','pending')";
    if (!mysqli_query($conn, $q1)) {
        throw new Exception('Gagal update pembayaran: ' . mysqli_error($conn));
    }

    // Update pemesanan: set status_bayar = 'lunas' and total_bayar = total_harga if not set
    $q2 = "UPDATE pemesanan SET status_bayar = 'lunas', total_bayar = COALESCE(total_bayar, total_harga) WHERE kode_booking = '$kode_booking'";
    if (!mysqli_query($conn, $q2)) {
        throw new Exception('Gagal update pemesanan: ' . mysqli_error($conn));
    }

    // Insert booking log
    $resP = mysqli_query($conn, "SELECT id_pemesanan FROM pemesanan WHERE kode_booking = '$kode_booking' LIMIT 1");
    if ($resP && mysqli_num_rows($resP) > 0) {
        $row = mysqli_fetch_assoc($resP);
        $idPemesanan = mysqli_real_escape_string($conn, $row['id_pemesanan']);
        mysqli_query($conn, "INSERT INTO booking_log (id_pemesanan, aktivitas, keterangan) VALUES ('$idPemesanan', 'Pembayaran Diverifikasi', 'Status pemesanan menjadi lunas oleh admin')");
    }

    mysqli_commit($conn);
    $_SESSION['success_message'] = 'Pembayaran berhasil diverifikasi untuk ' . $kode_booking;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error_message'] = 'Verifikasi gagal: ' . $e->getMessage();
}

header('Location: dashboard.php');
exit;
