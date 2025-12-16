<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Support both ID dan nama kota
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$kota_name = isset($_GET['kota']) ? mysqli_real_escape_string($conn, $_GET['kota']) : null;

if ($id) {
    $data = mysqli_query($conn, "SELECT * FROM destinasi WHERE id=$id");
} else if ($kota_name) {
    $data = mysqli_query($conn, "SELECT * FROM destinasi WHERE kota='$kota_name'");
} else {
    header("Location: ../wisata.php");
    exit;
}

$d = mysqli_fetch_assoc($data);

if (!$d) {
    header("Location: ../wisata.php");
    exit;
}

if (isset($_POST['update'])) {
    $kota_baru = mysqli_real_escape_string($conn, $_POST['kota']);
    $gambar = mysqli_real_escape_string($conn, $_POST['gambar']);

    $query = "UPDATE destinasi SET kota='$kota_baru', gambar='$gambar' WHERE id=" . $d['id'];
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Destinasi berhasil diperbarui";
        header("Location: ../wisata.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Destinasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light py-5">

<div class="container" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Destinasi</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kota <span class="text-danger">*</span></label>
                    <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($d['kota']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Path Gambar <span class="text-danger">*</span></label>
                    <input type="text" name="gambar" class="form-control" value="<?= htmlspecialchars($d['gambar']) ?>" placeholder="img/batu.webp" required>
                    <small class="text-muted">Format: img/nama_file.ext</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="../wisata.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" name="update" class="btn btn-primary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                        <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
