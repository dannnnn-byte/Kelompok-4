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

$uploadDir = __DIR__ . '/../img/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transport'])) {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan'] ?? '');
    $kapasitas = isset($_POST['kapasitas_kursi']) && $_POST['kapasitas_kursi'] !== ''
        ? (int)$_POST['kapasitas_kursi']
        : null;
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas_mobil'] ?? '');
    $gambarName = null;

    if (empty($jenis)) {
        $error = "Jenis kendaraan wajib diisi";
    }

    // Handle upload bila ada
    if (!isset($error) && isset($_FILES['gambar_transport']) && $_FILES['gambar_transport']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_transport'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $error = "Format gambar harus JPG/PNG/WebP";
        } else {
            $gambarName = 'transport_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $targetPath = $uploadDir . $gambarName;
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $error = "Gagal mengunggah gambar";
            }
        }
    }

    if (!isset($error)) {
        $query = "INSERT INTO master_transport (jenis_kendaraan, kapasitas_kursi, fasilitas_mobil, gambar_transport) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('siss', $jenis, $kapasitas, $fasilitas, $gambarName);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Transportasi berhasil ditambahkan";
            header("Location: transportasi_list.php");
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
            <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Tambah Transportasi</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="submit_transport" value="1">

                <div class="mb-3">
                    <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="jenis_kendaraan" placeholder="Contoh: Toyota Hiace Commuter" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kapasitas Kursi</label>
                    <input type="number" class="form-control" name="kapasitas_kursi" min="1" placeholder="Contoh: 14">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fasilitas</label>
                    <textarea class="form-control" name="fasilitas_mobil" rows="3" placeholder="AC, Karaoke, Reclining Seat"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar (opsional)</label>
                    <input type="file" class="form-control" name="gambar_transport" accept="image/*">
                    <small class="text-muted">Format: JPG/PNG/WebP</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="transportasi_list.php" class="btn btn-secondary">
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
