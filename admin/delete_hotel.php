<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

/* ================= PROSES DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_hotel'])) {
    $id_hotel = mysqli_real_escape_string($conn, $_POST['id_hotel']);

    // Ambil gambar untuk dihapus
    $res = mysqli_query($conn, "SELECT gambar_hotel FROM master_hotel WHERE id_hotel = '$id_hotel'");
    $row = mysqli_fetch_assoc($res);
    $gambar = $row['gambar_hotel'] ?? null;

    $query = "DELETE FROM master_hotel WHERE id_hotel = '$id_hotel'";
    if (mysqli_query($conn, $query)) {
        // Hapus file gambar jika ada
        $uploadDir = __DIR__ . '/../img/';
        if (!empty($gambar) && file_exists($uploadDir . $gambar)) {
            @unlink($uploadDir . $gambar);
        }
        $_SESSION['success_message'] = "Hotel berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

header("Location: hotel_list.php");
exit;
?>
