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

    // Ambil paket wisata terkait untuk bersihkan file gambar
    $paket_q = mysqli_query($conn, "SELECT gambar_paket FROM paket_wisata WHERE id_kota = '$id_kota'");
    while ($paket = mysqli_fetch_assoc($paket_q)) {
        if (!empty($paket['gambar_paket'])) {
            $path = '../img/' . $paket['gambar_paket'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    // Hapus paket wisata yang terkait kota ini
    mysqli_query($conn, "DELETE FROM paket_wisata WHERE id_kota = '$id_kota'");

    // Ambil gambar kota untuk dibersihkan setelah delete
    $kota_q = mysqli_query($conn, "SELECT gambar_kota FROM kota WHERE id_kota = '$id_kota' LIMIT 1");
    $kota_img = mysqli_fetch_assoc($kota_q)['gambar_kota'] ?? null;

    $query = "DELETE FROM kota WHERE id_kota = '$id_kota'";
    if (mysqli_query($conn, $query)) {
        if (!empty($kota_img)) {
            $pathKota = '../img/' . $kota_img;
            if (file_exists($pathKota)) {
                @unlink($pathKota);
            }
        }
        $_SESSION['success_message'] = "Kota dan paket terkait berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

header("Location: kota_list.php");
exit;
?>
