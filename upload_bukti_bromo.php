<?php
session_start();
include 'koneksi.php';

$bromo_id = intval($_POST['bromo_id']);

if (!isset($_FILES['bukti_bayar']) || $_FILES['bukti_bayar']['error'] !== 0) {
    header("Location: pembayaran_bromo.php");
    exit;
}

/* ================= SANITASI NAMA FILE ================= */
$nama_asli = $_FILES['bukti_bayar']['name'];

// ganti spasi jadi underscore
$nama_asli = str_replace(' ', '_', $nama_asli);

// hapus karakter aneh
$nama_asli = preg_replace("/[^a-zA-Z0-9._-]/", "", $nama_asli);

// nama file final
$nama_file = time() . "_" . $nama_asli;

/* ================= UPLOAD ================= */
$tmp    = $_FILES['bukti_bayar']['tmp_name'];
$folder = "uploads/bukti_bromo/";

move_uploaded_file($tmp, $folder . $nama_file);

/* ================= SIMPAN DB ================= */
$query = "INSERT INTO pembayaran_bromo (bromo_id, bukti_bayar, status_bayar)
          VALUES ($bromo_id, '$nama_file', 'menunggu_verifikasi')";
mysqli_query($conn, $query);

/* ================= REDIRECT ================= */
header("Location: payment_success_bromo.php?id=$bromo_id");
exit;
