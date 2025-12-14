<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM destinasi WHERE id=$id");
$d = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $kota   = $_POST['kota'];
    $gambar = $_POST['gambar'];

    mysqli_query($conn, "UPDATE destinasi SET kota='$kota', gambar='$gambar' WHERE id=$id");
    header("Location: ../wisata.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Destinasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

<h3 class="fw-bold mb-4">Edit Destinasi</h3>

<form method="POST">
  <div class="mb-3">
    <label class="form-label">Nama Kota</label>
    <input type="text" name="kota" class="form-control" value="<?= $d['kota'] ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Path Gambar</label>
    <input type="text" name="gambar" class="form-control" value="<?= $d['gambar'] ?>" required>
  </div>

  <button name="update" class="btn btn-warning">Update</button>
  <a href="../wisata.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
