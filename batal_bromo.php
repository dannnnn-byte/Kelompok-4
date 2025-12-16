<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    UPDATE pemesanan_bromo
    SET status='cancelled', waktu_batal=NOW()
    WHERE id=? AND user_id=? AND status='pending'
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: riwayat_bromo.php");
exit;
