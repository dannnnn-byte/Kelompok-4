<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

/* ================= PROSES DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_kota'])) {
    $id_kota = mysqli_real_escape_string($conn, $_POST['id_kota']);
    
    // Cek apakah kota memiliki paket wisata
    $check_query = "SELECT COUNT(*) as total FROM paket_wisata WHERE id_kota = '$id_kota'";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['total'] > 0) {
        $_SESSION['error_message'] = "Kota tidak dapat dihapus karena masih memiliki paket wisata";
    } else {
        $query = "DELETE FROM kota WHERE id_kota = '$id_kota'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kota berhasil dihapus";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: kota_list.php");
exit;
?>
