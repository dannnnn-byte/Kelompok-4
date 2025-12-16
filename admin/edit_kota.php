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
    
    if (empty($nama_kota)) {
        $error = "Nama kota tidak boleh kosong";
    } else {
        $query = "UPDATE kota SET nama_kota = '$nama_kota' WHERE id_kota = '$id_kota'";
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

            <form method="POST">
                <input type="hidden" name="submit_kota" value="1">
                
                <div class="mb-3">
                    <label class="form-label">Nama Kota <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           name="nama_kota" 
                           value="<?= htmlspecialchars($kota['nama_kota']) ?>"
                           required>
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
