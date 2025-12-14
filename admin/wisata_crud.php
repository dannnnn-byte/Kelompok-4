<?php
require_once '../koneksi.php';
require_once '../includes/auth.php';

onlyAdmin(); // 🔒 USER TIDAK BISA MASUK
?>

<!DOCTYPE html>
<html>
<head>
<title>CRUD Wisata - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">Kelola Wisata</h3>

    <a href="tambah_wisata.php" class="btn btn-success mb-3">+ Tambah Wisata</a>

    <table class="table table-bordered">
        <tr>
            <th>Nama Wisata</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>

        <?php
        $data = mysqli_query($conn, "SELECT * FROM wisata");
        while ($w = mysqli_fetch_assoc($data)) {
        ?>
        <tr>
            <td><?= $w['nama_wisata']; ?></td>
            <td><?= $w['lokasi']; ?></td>
            <td>
                <a href="edit_wisata.php?id=<?= $w['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_wisata.php?id=<?= $w['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
