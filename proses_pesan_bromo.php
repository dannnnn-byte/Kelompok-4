<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['pesan_bromo'])) {
    $user_id = $_POST['user_id'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $jumlah_orang = $_POST['jumlah_orang'];
    $sewa_jeep = $_POST['sewa_jeep'];
    $sewa_trail = $_POST['sewa_trail'];
    $jumlah_trail = $_POST['jumlah_trail'];
    $total_harga = $_POST['total_harga'];
    $created_at = date('Y-m-d H:i:s');

    // Insert ke tabel pemesanan_bromo
    $stmt = $conn->prepare("INSERT INTO pemesanan_bromo (user_id, tanggal_kunjungan, jumlah_orang, sewa_jeep, sewa_trail, jumlah_trail, total_harga, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
    $stmt->bind_param("isissids", $user_id, $tanggal_kunjungan, $jumlah_orang, $sewa_jeep, $sewa_trail, $jumlah_trail, $total_harga, $created_at);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Pemesanan berhasil dibuat. Silakan lakukan pembayaran.";
        header("Location: bayar_bromo.php?id=" . $stmt->insert_id);
        exit;
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan: " . $stmt->error;
        header("Location: bromo.php");
        exit;
    }
} else {
    header("Location: bromo.php");
    exit;
}
?>
