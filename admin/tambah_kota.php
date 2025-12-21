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

/* ================= PROSES TAMBAH ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_kota'])) {
    $nama_kota = mysqli_real_escape_string($conn, $_POST['nama_kota']);

    if (empty($nama_kota)) {
        $error = "Nama kota tidak boleh kosong";
    }

    // Validasi upload gambar kota (wajib, max 5MB)
    if (!isset($error)) {
        if (empty($_FILES['gambar_kota']['name'])) {
            $error = "Gambar kota wajib diupload";
        } else {
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
    }

    if (!isset($error)) {
        // Simpan nama kota dan file gambar ke tabel kota
        $query = "INSERT INTO kota (nama_kota, gambar_kota) VALUES ('$nama_kota', '$gambar_kota')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kota berhasil ditambahkan";
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Tambah Kota Baru</h5>
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
                           placeholder="Contoh: Malang, Surabaya, Jakarta"
                           required>
                    <small class="form-text text-muted">Masukkan nama kota destinasi wisata</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Kota <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="gambar_kota" accept="image/*" required>
                    <small class="text-muted">Format: JPG/PNG/WebP, max 5MB</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="kota_list.php" class="btn btn-secondary">
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
