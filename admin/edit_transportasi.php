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

if (!isset($_GET['id'])) {
    header("Location: transportasi_list.php");
    exit;
}

$id_transport = mysqli_real_escape_string($conn, $_GET['id']);
$transport = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM master_transport WHERE id_transport = '$id_transport'"));

if (!$transport) {
    $_SESSION['error_message'] = "Data transportasi tidak ditemukan";
    header("Location: transportasi_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transport'])) {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan'] ?? '');
    $kapasitas = isset($_POST['kapasitas_kursi']) && $_POST['kapasitas_kursi'] !== ''
        ? (int)$_POST['kapasitas_kursi']
        : null;
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas_mobil'] ?? '');
    $gambarName = $transport['gambar_transport'];

    if (empty($jenis)) {
        $error = "Jenis kendaraan wajib diisi";
    }

    // Upload baru jika ada
    if (!isset($error) && isset($_FILES['gambar_transport']) && $_FILES['gambar_transport']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_transport'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $error = "Format gambar harus JPG/PNG/WebP";
        } else {
            $newName = 'transport_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $targetPath = $uploadDir . $newName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Hapus gambar lama jika ada
                if (!empty($gambarName) && file_exists($uploadDir . $gambarName)) {
                    @unlink($uploadDir . $gambarName);
                }
                $gambarName = $newName;
            } else {
                $error = "Gagal mengunggah gambar";
            }
        }
    }

    if (!isset($error)) {
        $query = "UPDATE master_transport SET jenis_kendaraan = ?, kapasitas_kursi = ?, fasilitas_mobil = ?, gambar_transport = ? WHERE id_transport = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sissi', $jenis, $kapasitas, $fasilitas, $gambarName, $id_transport);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Transportasi berhasil diperbarui";
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
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Transportasi</h5>
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
                    <input type="text" class="form-control" name="jenis_kendaraan" value="<?= htmlspecialchars($transport['jenis_kendaraan']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kapasitas Kursi</label>
                    <input type="number" class="form-control" name="kapasitas_kursi" min="1" value="<?= htmlspecialchars($transport['kapasitas_kursi']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fasilitas</label>
                    <textarea class="form-control" name="fasilitas_mobil" rows="3"><?= htmlspecialchars($transport['fasilitas_mobil']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar (opsional)</label>
                    <input type="file" class="form-control" name="gambar_transport" accept="image/*">
                    <?php if (!empty($transport['gambar_transport'])): ?>
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Gambar saat ini:</small>
                            <img src="../uploads/transport/<?= urlencode($transport['gambar_transport']) ?>" alt="gambar" style="width:120px; height:90px; object-fit:cover; border-radius:6px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <a href="transportasi_list.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                        <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
