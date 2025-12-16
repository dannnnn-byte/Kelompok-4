<?php
session_start();
include 'koneksi.php';

$bromo_id = intval($_POST['bromo_id']);

if (!isset($_FILES['bukti_bayar'])) {
    header("Location: pembayaran_bromo.php");
    exit;
}

/* upload file */
$nama_file = time() . '_' . $_FILES['bukti_bayar']['name'];
$tmp       = $_FILES['bukti_bayar']['tmp_name'];
$folder    = "uploads/bukti_bromo/";

move_uploaded_file($tmp, $folder . $nama_file);

/* simpan ke database */
$query = "INSERT INTO pembayaran_bromo (bromo_id, bukti_bayar, status_bayar)
          VALUES ($bromo_id, '$nama_file', 'menunggu_verifikasi')";
mysqli_query($conn, $query);

/* ✅ REDIRECT KE SUCCESS */
header("Location: payment_success_bromo.php?id=$bromo_id");
exit;
