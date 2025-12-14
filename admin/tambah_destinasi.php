<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $kota   = $_POST['kota'];
    $gambar = $_POST['gambar'];

    mysqli_query($conn, "INSERT INTO destinasi (kota, gambar) VALUES ('$kota', '$gambar')");
    header("Location: ../wisata.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Destinasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

<h3 class="fw-bold mb-4">Tambah Destinasi Wisata</h3>

<form method="POST">
  <div class="mb-3">
    <label class="form-label">Nama Kota</label>
    <input type="text" name="kota" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Path Gambar</label>
    <input type="text" name="gambar" class="form-control" placeholder="img/batu.webp" required>
  </div>

  <button name="simpan" class="btn btn-success">Simpan</button>
  <a href="../wisata.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
