<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM destinasi WHERE id=$id");

header("Location: ../wisata.php");
exit;
