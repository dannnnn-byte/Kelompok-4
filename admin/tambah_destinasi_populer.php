<?php
session_start();
include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$alert = "";
?>

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

          <?= $alert ?>

          <form method="POST" enctype="multipart/form-data">

            <!-- Nama Destinasi -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Nama Destinasi</label>
              <input type="text"
                     name="nama"
                     class="form-control"
                     placeholder="Contoh: Pantai Papuma"
                     required>
            </div>

            <!-- Slug -->
            <div class="mb-3">
              <label class="form-label fw-semibold">
                Slug <small class="text-muted">(opsional)</small>
              </label>
              <input type="text"
                     name="slug"
                     class="form-control"
                     placeholder="pantai-papuma">
              <small class="text-muted">
                Jika kosong, slug akan dibuat otomatis.
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

