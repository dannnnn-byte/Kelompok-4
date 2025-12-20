<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

$uploadDir = __DIR__ . '/../uploads/hotel/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_hotel'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_hotel'] ?? '');
  $bintang = isset($_POST['bintang']) && $_POST['bintang'] !== '' ? (int)$_POST['bintang'] : null;
  $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
  $gambarName = null;

  if (empty($nama)) {
    $error = "Nama hotel wajib diisi";
  }

  // Upload file jika ada
  if (!isset($error) && isset($_FILES['gambar_hotel']) && $_FILES['gambar_hotel']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['gambar_hotel'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) {
      $error = "Format gambar harus JPG/PNG/WebP";
    } else {
      $gambarName = 'hotel_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $targetPath = $uploadDir . $gambarName;
      if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $error = "Gagal mengunggah gambar";
      }
    }
  }

  if (!isset($error)) {
    $stmt = $conn->prepare("INSERT INTO master_hotel (nama_hotel, bintang, lokasi, gambar_hotel) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $nama, $bintang, $lokasi, $gambarName);
    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Hotel berhasil ditambahkan";
      header("Location: hotel_list.php");
      exit;
    } else {
      $error = "Error: " . $stmt->error;
    }
    $stmt->close();
  }
}
?>

<div class="container py-5" style="max-width: 700px;">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Tambah Hotel</h5>
    </div>
    <div class="card-body">
      <?php if (isset($error)): ?>
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="submit_hotel" value="1">

        <div class="mb-3">
          <label class="form-label">Nama Hotel <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="nama_hotel" placeholder="Contoh: Hotel Tugu Malang" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Bintang</label>
          <input type="number" class="form-control" name="bintang" min="1" max="5" placeholder="1-5">
        </div>

        <div class="mb-3">
          <label class="form-label">Lokasi</label>
          <input type="text" class="form-control" name="lokasi" placeholder="Contoh: Kota Malang">
        </div>

        <div class="mb-3">
          <label class="form-label">Gambar (opsional)</label>
          <input type="file" class="form-control" name="gambar_hotel" accept="image/*">
          <small class="text-muted">Format: JPG/PNG/WebP</small>
        </div>

        <div class="d-flex gap-2">
          <a href="hotel_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Batal
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer_admin.php'; ?>
