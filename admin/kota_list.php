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

/* ================= STATISTIK ================= */
$q_count = mysqli_query($conn, "SELECT COUNT(*) total FROM kota");
$total_kota = mysqli_fetch_assoc($q_count)['total'] ?? 0;

/* ================= QUERY DATA KOTA ================= */
$query = "SELECT * FROM kota ORDER BY id_kota DESC";
$data_kota = mysqli_query($conn, $query);
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

    <!-- ================= HEADER ================= -->
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h2 class="fw-bold mb-0">Kelola Destinasi / Kota</h2>
            <p class="text-muted mb-0">Total Destinasi: <strong><?= $total_kota ?></strong></p>
        </div>
        <a href="tambah_kota.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Kota
        </a>
    </div>

    <!-- ================= TABEL KOTA ================= -->
    <div class="card shadow">
        <div class="card-body">
            <?php if (mysqli_num_rows($data_kota) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Kota</th>
                            <th>Tanggal Dibuat</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data_kota)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_kota']) ?></strong></td>
                            <td><?= isset($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-' ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_kota.php?id=<?= $row['id_kota'] ?>" 
                                       class="btn btn-sm btn-warning fw-bold"
                                       title="Edit Kota"
                                       style="padding: 6px 12px; border-radius: 5px;">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form method="POST" action="delete_kota.php" class="d-inline">
                                        <input type="hidden" name="id_kota" value="<?= $row['id_kota'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold"
                                                onclick="return confirm('Yakin hapus kota ini?');"
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
                <i class="bi bi-info-circle"></i> Belum ada data kota. <a href="tambah_kota.php">Tambah kota baru</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
