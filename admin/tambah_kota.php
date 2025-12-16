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
    } else {
        $query = "INSERT INTO kota (nama_kota, created_at) VALUES ('$nama_kota', NOW())";
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

            <form method="POST">
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
