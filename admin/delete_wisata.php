<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

/* ================= PROSES DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_paket'])) {
    $id_paket = mysqli_real_escape_string($conn, $_POST['id_paket']);
    
    // Cek apakah paket memiliki pemesanan
    $check_query = "SELECT COUNT(*) as total FROM pemesanan WHERE id_paket = '$id_paket'";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['total'] > 0) {
        $_SESSION['error_message'] = "Paket wisata tidak dapat dihapus karena masih memiliki pemesanan";
    } else {
        $query = "DELETE FROM paket_wisata WHERE id_paket = '$id_paket'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Paket wisata berhasil dihapus";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: wisata_list.php");
exit;
?>
