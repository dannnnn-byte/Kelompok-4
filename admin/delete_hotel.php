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
    
    $query = "DELETE FROM hotel WHERE id = '$id_hotel'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Hotel berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

header("Location: hotel_list.php");
exit;
?>
