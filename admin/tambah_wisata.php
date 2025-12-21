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

// Ambil daftar kota
$query_kota = "SELECT * FROM kota ORDER BY nama_kota ASC";
$result_kota = mysqli_query($conn, $query_kota);

/* ================= PROSES TAMBAH ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_wisata'])) {
    $id_kota = (int)$_POST['id_kota'];
    $nama_paket = mysqli_real_escape_string($conn, $_POST['nama_paket']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);
    $harga = (int)$_POST['harga'];
    
    // Handle gambar
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../img/";
        $gambar_file = basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $gambar_file;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $gambar_file;
        } else {
            $error = "Gagal mengupload gambar";
        }
    }
    
    if (!isset($error)) {
        // Kolom deskripsi di tabel adalah deskripsi_wisata
        $query = "INSERT INTO paket_wisata (id_kota, nama_paket, deskripsi_wisata, durasi, harga_per_pax, gambar_paket) 
              VALUES ($id_kota, '$nama_paket', '$deskripsi', '$durasi', $harga, '$gambar')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Paket wisata berhasil ditambahkan";
            header("Location: wisata_list.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container py-5" style="max-width: 800px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Tambah Paket Wisata Baru</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="submit_wisata" value="1">
                
                <div class="mb-3">
                    <label class="form-label">Kota Destinasi <span class="text-danger">*</span></label>
                    <select class="form-control" name="id_kota" required>
                        <option value="">-- Pilih Kota --</option>
                        <?php while ($kota = mysqli_fetch_assoc($result_kota)): ?>
                        <option value="<?= $kota['id_kota'] ?>"><?= htmlspecialchars($kota['nama_kota']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="nama_paket" 
                           placeholder="Contoh: Batu 2 Hari 1 Malam"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" 
                              name="deskripsi" 
                              rows="4" 
                              placeholder="Deskripsi lengkap paket wisata"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Durasi <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="durasi" 
                               placeholder="Contoh: 2 Hari 1 Malam"
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Per Orang <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               name="harga" 
                               placeholder="500000"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Paket</label>
                    <input type="file" 
                           class="form-control" 
                           name="gambar"
                           accept="image/*">
                    <small class="text-muted">Format: JPG, PNG, WebP (Max 5MB)</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="wisata_list.php" class="btn btn-secondary">
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
