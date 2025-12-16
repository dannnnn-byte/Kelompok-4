<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Support both ID dan nama kota
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$kota = isset($_POST['kota']) ? mysqli_real_escape_string($conn, $_POST['kota']) : null;

if ($id) {
    $query = "DELETE FROM destinasi WHERE id=$id";
} else if ($kota) {
    $query = "DELETE FROM destinasi WHERE kota='$kota'";
} else {
    header("Location: ../wisata.php");
    exit;
}

if (mysqli_query($conn, $query)) {
    $_SESSION['success_message'] = "Destinasi berhasil dihapus";
} else {
    $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
}

header("Location: ../wisata.php");
exit;
?>
