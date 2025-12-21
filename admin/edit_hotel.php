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
$query = "SELECT * FROM master_hotel WHERE id_hotel = '$id_hotel'";
$result = mysqli_query($conn, $query);
$hotel = mysqli_fetch_assoc($result);

if (!$hotel) {
    $_SESSION['error_message'] = "Hotel tidak ditemukan";
    header("Location: hotel_list.php");
    exit;
}

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hotel'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_hotel']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $bintang = isset($_POST['bintang']) && $_POST['bintang'] !== '' ? (int)$_POST['bintang'] : null;

    $uploadDir = __DIR__ . '/../img/';

    // Handle gambar
    $gambar = $hotel['gambar_hotel']; // Default ke gambar lama
    if (!empty($_FILES['gambar_hotel']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar_hotel']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $error = "Format gambar harus JPG/PNG/WebP";
        } else {
            $newName = 'hotel_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $target_file = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['gambar_hotel']['tmp_name'], $target_file)) {
                if (!empty($gambar) && file_exists($uploadDir . $gambar)) {
                    @unlink($uploadDir . $gambar);
                }
                $gambar = $newName;
            } else {
                $error = "Gagal mengupload gambar";
            }
        }
    }
    
    if (!isset($error)) {
        $query_update = "UPDATE master_hotel SET 
            nama_hotel = ?,
            lokasi = ?,
            bintang = ?,
            gambar_hotel = ?
        WHERE id_hotel = ?";
        $stmt = $conn->prepare($query_update);
        $stmt->bind_param('sissi', $nama, $lokasi, $bintang, $gambar, $id_hotel);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Hotel berhasil diperbarui";
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
                           name="nama_hotel" 
                           value="<?= htmlspecialchars($hotel['nama_hotel']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" 
                           class="form-control" 
                           name="lokasi" 
                           value="<?= htmlspecialchars($hotel['lokasi']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Bintang</label>
                    <input type="number" 
                           class="form-control" 
                           name="bintang" 
                           min="1" max="5"
                           value="<?= htmlspecialchars($hotel['bintang']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    <?php if (!empty($hotel['gambar_hotel'])): ?>
                    <div class="mb-2">
                        <img src="../uploads/hotel/<?= htmlspecialchars($hotel['gambar_hotel']) ?>" style="max-width: 200px;" class="rounded">
                        <p class="text-muted small">Gambar saat ini</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" 
                           class="form-control" 
                           name="gambar_hotel"
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
