<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

// Pastikan user login
if (!isset($_SESSION['username'])) {
    die("Anda harus login untuk mengirim ulasan.");
}

// Ambil data dari form
$tempat    = "bromo"; // tetap "bromo"
$rating    = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$komentar  = isset($_POST['komentar']) ? trim($_POST['komentar']) : "";
$namaUser  = $_SESSION['username'];

// Validasi
if ($rating < 1 || $rating > 5 || empty($komentar)) {
    die("Data tidak lengkap atau rating tidak valid.");
}

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO reviews (tempat, nama, rating, komentar) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $tempat, $namaUser, $rating, $komentar);

if ($stmt->execute()) {
    // Redirect ke halaman bromo setelah sukses
    header("Location: bromo.php?success=1");
    exit();
} else {
    echo "Gagal menyimpan ulasan: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
