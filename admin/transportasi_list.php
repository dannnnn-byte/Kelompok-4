<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Hitung total transportasi
$q_count = mysqli_query($conn, "SELECT COUNT(*) AS total FROM master_transport");
$total_transport = mysqli_fetch_assoc($q_count)['total'] ?? 0;

// Ambil data transport
$data_transport = mysqli_query($conn, "SELECT * FROM master_transport ORDER BY id_transport DESC");
?>

<div class="container py-5">

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0">Kelola Transportasi</h2>
            <p class="text-muted mb-0">Total armada: <strong><?= $total_transport ?></strong></p>
        </div>
        <a href="tambah_transportasi.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Transportasi
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (mysqli_num_rows($data_transport) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Jenis Kendaraan</th>
                            <th>Kapasitas</th>
                            <th>Fasilitas</th>
                            <th>Gambar</th>
                            <th style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data_transport)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['jenis_kendaraan']) ?></strong></td>
                            <td><?= $row['kapasitas_kursi'] ? intval($row['kapasitas_kursi']) . ' kursi' : '-' ?></td>
                            <td><?= $row['fasilitas_mobil'] ? nl2br(htmlspecialchars($row['fasilitas_mobil'])) : '-' ?></td>
                            <td>
                                <?php if (!empty($row['gambar_transport'])): ?>
                                    <img src="../img/<?= urlencode($row['gambar_transport']) ?>" alt="gambar" style="width:150px; height:110px; object-fit:cover; border-radius:8px;">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_transportasi.php?id=<?= $row['id_transport'] ?>" 
                                       class="btn btn-sm btn-warning fw-bold"
                                       title="Edit Transportasi"
                                       style="padding: 6px 12px; border-radius: 5px;">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form method="POST" action="delete_transportasi.php" class="d-inline">
                                        <input type="hidden" name="id_transport" value="<?= $row['id_transport'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold"
                                                onclick="return confirm('Yakin hapus transportasi ini?');"
                                                style="padding: 6px 12px; border-radius: 5px;">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada data transportasi. <a href="tambah_transportasi.php">Tambah transportasi baru</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
