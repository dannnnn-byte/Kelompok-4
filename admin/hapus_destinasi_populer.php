<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['id'])) {
    header("Location: ../../index.php");
    exit;
}

$id = intval($_POST['id']);

// Soft delete
mysqli_query($conn,
  "UPDATE destinasi_populer SET aktif=0 WHERE id=$id");

header("Location: ../index.php");
exit;
