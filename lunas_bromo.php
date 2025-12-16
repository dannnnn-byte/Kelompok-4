<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin/dashboard.php");
    exit;
}

$id = intval($_GET['id']);

// Update status menjadi paid
$query = "UPDATE pemesanan_bromo SET status='paid' WHERE id=$id";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: admin/dashboard.php?msg=updated");
} else {
    die("Error update: " . mysqli_error($conn));
}
