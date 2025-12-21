<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transport'])) {
    $id_transport = mysqli_real_escape_string($conn, $_POST['id_transport']);

    // Ambil data untuk hapus file
    $res = mysqli_query($conn, "SELECT gambar_transport FROM master_transport WHERE id_transport = '$id_transport'");
    $row = mysqli_fetch_assoc($res);
    $gambar = $row['gambar_transport'] ?? null;

    // Hapus data
    if (mysqli_query($conn, "DELETE FROM master_transport WHERE id_transport = '$id_transport'")) {
        // Hapus file gambar jika ada
        $uploadDir = __DIR__ . '/../img/';
        if (!empty($gambar) && file_exists($uploadDir . $gambar)) {
            @unlink($uploadDir . $gambar);
        }
        $_SESSION['success_message'] = "Transportasi berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

header("Location: transportasi_list.php");
exit;
