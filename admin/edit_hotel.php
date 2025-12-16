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
    header("Location: hotel_list.php");
    exit;
}

$id_hotel = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM hotel WHERE id = '$id_hotel'";
$result = mysqli_query($conn, $query);
$hotel = mysqli_fetch_assoc($result);

if (!$hotel) {
    $_SESSION['error_message'] = "Hotel tidak ditemukan";
    header("Location: hotel_list.php");
    exit;
}

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hotel'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga = (int)$_POST['harga'];
    
    // Handle gambar
    $gambar = $hotel['gambar']; // Default ke gambar lama
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../img/";
        $gambar_file = basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $gambar_file;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = "img/" . $gambar_file;
        } else {
            $error = "Gagal mengupload gambar";
        }
    }
    
    if (!isset($error)) {
        $query_update = "UPDATE hotel SET 
            nama = '$nama',
            lokasi = '$lokasi',
            harga = $harga,
            gambar = '$gambar'
        WHERE id = '$id_hotel'";
        
        if (mysqli_query($conn, $query_update)) {
            $_SESSION['success_message'] = "Hotel berhasil diperbarui";
            header("Location: hotel_list.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container py-5" style="max-width: 700px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Hotel</h5>
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
                    <input type="text" 
                           class="form-control" 
                           name="nama" 
                           value="<?= htmlspecialchars($hotel['nama']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="lokasi" 
                           value="<?= htmlspecialchars($hotel['lokasi']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control" 
                           name="harga" 
                           value="<?= $hotel['harga'] ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    <?php if (!empty($hotel['gambar'])): ?>
                    <div class="mb-2">
                        <img src="../<?= htmlspecialchars($hotel['gambar']) ?>" style="max-width: 200px;" class="rounded">
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
                    <a href="hotel_list.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
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
