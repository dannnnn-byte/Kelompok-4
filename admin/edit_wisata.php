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
    header("Location: wisata_list.php");
    exit;
}

$id_paket = (int)$_GET['id'];
$query = "SELECT * FROM paket_wisata WHERE id_paket = $id_paket";
$result = mysqli_query($conn, $query);
$paket = mysqli_fetch_assoc($result);

if (!$paket) {
    $_SESSION['error_message'] = "Paket wisata tidak ditemukan";
    header("Location: wisata_list.php");
    exit;
}

// Ambil daftar kota
$query_kota = "SELECT * FROM kota ORDER BY nama_kota ASC";
$result_kota = mysqli_query($conn, $query_kota);

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_wisata'])) {
    $id_kota = (int)$_POST['id_kota'];
    $nama_paket = mysqli_real_escape_string($conn, $_POST['nama_paket']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);
    $harga = (int)$_POST['harga'];
    
    $gambar = $paket['gambar_paket']; // Default ke gambar lama
    
    // Handle gambar baru
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
        $query_update = "UPDATE paket_wisata SET 
            id_kota = $id_kota,
            nama_paket = '$nama_paket',
            deskripsi = '$deskripsi',
            durasi = '$durasi',
            harga_per_pax = $harga,
            gambar_paket = '$gambar'
        WHERE id_paket = $id_paket";
        
        if (mysqli_query($conn, $query_update)) {
            $_SESSION['success_message'] = "Paket wisata berhasil diperbarui";
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
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Paket Wisata</h5>
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
                        <?php 
                        mysqli_data_seek($result_kota, 0);
                        while ($kota = mysqli_fetch_assoc($result_kota)): 
                        ?>
                        <option value="<?= $kota['id_kota'] ?>" 
                                <?= $kota['id_kota'] == $paket['id_kota'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kota['nama_kota']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="nama_paket" 
                           value="<?= htmlspecialchars($paket['nama_paket']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" 
                              name="deskripsi" 
                              rows="4"><?= htmlspecialchars($paket['deskripsi']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Durasi <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="durasi" 
                               value="<?= htmlspecialchars($paket['durasi']) ?>"
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Per Orang <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               name="harga" 
                               value="<?= $paket['harga_per_pax'] ?>"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar Paket</label>
                    <?php if (!empty($paket['gambar_paket'])): ?>
                    <div class="mb-2">
                        <img src="../img/<?= htmlspecialchars($paket['gambar_paket']) ?>" style="max-width: 200px;" class="rounded">
                        <p class="text-muted small">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" 
                           class="form-control" 
                           name="gambar"
                           accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="wisata_list.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
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
