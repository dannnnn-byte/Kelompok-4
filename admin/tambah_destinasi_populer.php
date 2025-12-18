<?php
session_start();
include '../koneksi.php';

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['simpan'])) {

    /* ================= VALIDASI ================= */
    $nama = trim($_POST['nama'] ?? '');
    $file = trim($_POST['file_tujuan'] ?? '');

    if ($nama === '' || $file === '') {
        echo "<script>alert('Nama destinasi & nama file wajib diisi'); window.history.back();</script>";
        exit;
    }

    /* ================= NORMALISASI FILE ================= */
    $slug = strtolower(preg_replace('/[^a-z0-9]/', '', $file));

    if ($slug === '') {
        echo "<script>alert('Nama file tidak valid'); window.history.back();</script>";
        exit;
    }

    /* ================= VALIDASI GAMBAR ================= */
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== 0) {
        echo "<script>alert('Gambar tidak terupload'); window.history.back();</script>";
        exit;
    }

    $gambar = $_FILES['gambar'];
    $ext = strtolower(pathinfo($gambar['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Format gambar tidak valid'); window.history.back();</script>";
        exit;
    }

    if ($gambar['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran gambar maksimal 2MB'); window.history.back();</script>";
        exit;
    }

    /* ================= UPLOAD ================= */
    $namaFileGambar = time() . '-' . $slug . '.' . $ext;
    $folder = "../uploads/destinasi/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    move_uploaded_file($gambar['tmp_name'], $folder . $namaFileGambar);

    /* ================= SIMPAN DB ================= */
    mysqli_query($conn, "
        INSERT INTO destinasi_populer (nama, slug, gambar, aktif)
        VALUES ('$nama', '$slug', '$namaFileGambar', 1)
    ");

    header("Location: ../index.php");
    exit;
}
?>



<?php
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/dashboard_home.php';
?>

<style>
.bg-light {
    min-height: 100vh;
    background: 
        linear-gradient(
            rgba(0, 0, 0, 0.45),
            rgba(0, 0, 0, 0.45)
        ),
        url("../img/ijen4.jpg") no-repeat center center / cover;
}

.bg-light .card {
    backdrop-filter: blur(2px);
    border-radius: 16px;
}
</style>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Destinasi | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" href="../../img/jawatrip1.png">
</head>

<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">

      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-success text-white rounded-top-4">
          <h4 class="mb-0 fw-bold">➕ Tambah Destinasi Wisata</h4>
        </div>

        <div class="card-body p-4">


          <form method="POST" enctype="multipart/form-data">

            <!-- Nama Destinasi -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Nama Destinasi</label>
              <input type="text"
                     name="nama"
                     class="form-control"

                     required>
            </div>

           <!-- Nama File Tujuan -->
<div class="mb-3">
  <label class="form-label fw-semibold">
    Nama File Tujuan <span class="text-danger">*</span>
  </label>

  <input type="text"
         name="file_tujuan"
         class="form-control">

  <small class="text-muted">
    Contoh: <b>destinasi(php)
  </small>
</div>


            <!-- Upload Gambar -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Gambar Destinasi</label>
              <input type="file"
                     name="gambar"
                     class="form-control"
                     accept="image/*"
                     required>
              <small class="text-muted">
                Format JPG / PNG / WEBP (max 2MB)
              </small>
            </div>

            <!-- Tombol -->
            <div class="d-flex justify-content-between">
              <a href="index.php" class="btn btn-outline-secondary">
                ← Kembali
              </a>

              <button type="submit"
                      name="simpan"
                      class="btn btn-success fw-bold px-4">
                Simpan Destinasi
              </button>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html>

<?php
/* ================= SIMPAN DATA ================= */
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $slug = trim($_POST['slug']);

    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $nama));
    }

    // Upload gambar
    $gambar = $_FILES['gambar'];
    $ext = strtolower(pathinfo($gambar['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Format gambar tidak valid!');</script>";
        exit;
    }

    if ($gambar['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran gambar maksimal 2MB!');</script>";
        exit;
    }

    $namaFile = time() . '-' . $slug . '.' . $ext;
    $path = "../uploads/destinasi/" . $namaFile;

    move_uploaded_file($gambar['tmp_name'], $path);

    mysqli_query($conn, "
        INSERT INTO destinasi_populer (nama, slug, gambar, aktif)
        VALUES ('$nama', '$slug', '$namaFile', 1)
    ");

    echo "<script>
        alert('Destinasi berhasil ditambahkan!');
        window.location='../index.php';
    </script>";
}

include 'footer_admin.php';
?>

