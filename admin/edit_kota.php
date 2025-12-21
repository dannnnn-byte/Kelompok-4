<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

/* ================= AMBIL DATA ================= */
if (!isset($_GET['id'])) {
    header("Location: kota_list.php");
    exit;
}

$id_kota = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM kota WHERE id_kota = '$id_kota'";
$result = mysqli_query($conn, $query);
$kota = mysqli_fetch_assoc($result);

if (!$kota) {
    $_SESSION['error_message'] = "Kota tidak ditemukan";
    header("Location: kota_list.php");
    exit;
}

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_kota'])) {
    $nama_kota = mysqli_real_escape_string($conn, $_POST['nama_kota']);
    $gambar_kota = $kota['gambar_kota']; // default gunakan gambar lama
    
    if (empty($nama_kota)) {
        $error = "Nama kota tidak boleh kosong";
    }

    // Upload gambar baru (opsional) dengan batas 5MB
    if (!isset($error) && !empty($_FILES['gambar_kota']['name'])) {
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['gambar_kota']['size'] > $maxSize) {
            $error = "Ukuran gambar maksimal 5MB";
        } else {
            $target_dir = "../img/";
            $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '', basename($_FILES['gambar_kota']['name']));
            $gambar_file = time() . '-' . $safe_name;
            $target_file = $target_dir . $gambar_file;

            if (move_uploaded_file($_FILES['gambar_kota']['tmp_name'], $target_file)) {
                $gambar_kota = $gambar_file;
            } else {
                $error = "Gagal mengupload gambar";
            }
        }
    }

    if (!isset($error)) {
        $query = "UPDATE kota SET nama_kota = '$nama_kota', gambar_kota = '$gambar_kota' WHERE id_kota = '$id_kota'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kota berhasil diperbarui";
            header("Location: kota_list.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container py-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Kota</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="submit_kota" value="1">
                
                <div class="mb-3">
                    <label class="form-label">Nama Kota <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="nama_kota" 
                           value="<?= htmlspecialchars($kota['nama_kota']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Kota</label>
                    <?php if (!empty($kota['gambar_kota'])): ?>
                    <div class="mb-2">
                        <img src="../img/<?= htmlspecialchars($kota['gambar_kota']) ?>" style="max-width: 200px;" class="rounded">
                        <p class="text-muted small mb-1">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="gambar_kota" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti. Maks 5MB (JPG/PNG/WebP).</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="kota_list.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
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
