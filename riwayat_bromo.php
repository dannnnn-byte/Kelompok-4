<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

require 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/dashboard_home.php';

/* ================= AMBIL USER ID ================= */
$user_id = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
if (!$user_id) {
    die("ERROR: User ID tidak ditemukan.");
}

/* ================= QUERY RIWAYAT ================= */
$stmt = $conn->prepare("
    SELECT 
        pb.*,
        pay.bukti_bayar,
        pay.status_bayar
    FROM pemesanan_bromo pb
    LEFT JOIN pembayaran_bromo pay 
        ON pay.bromo_id = pb.id
    WHERE pb.user_id = ?
    ORDER BY pb.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result();

?>

<div class="container py-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Riwayat Pemesanan Bromo</h2>
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>

<?php if ($data->num_rows === 0): ?>
    <div class="alert alert-info">
        Anda belum memiliki riwayat pemesanan.
    </div>
<?php else: ?>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Tanggal Kunjungan</th>
    <th>Orang</th>
    <th>Jeep</th>
    <th>Trail</th>
    <th>Total</th>
    <th>Status</th>
    <th>Waktu Pesan</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>

<?php $no = 1; while ($r = $data->fetch_assoc()): ?>
<?php
// PRIORITAS STATUS FINAL
if ($r['status'] === 'paid') {
    $status = 'paid';
} elseif (!empty($r['bukti_bayar'])) {
    $status = 'menunggu_verifikasi';
} else {
    $status = $r['status'] ?? 'pending';
}

// badge
if ($status === 'paid') {
    $badge = 'bg-success';
} elseif ($status === 'menunggu_verifikasi') {
    $badge = 'bg-info';
} elseif ($status === 'cancelled') {
    $badge = 'bg-danger';
} else {
    $badge = 'bg-warning text-dark';
}
?>


<tr>
    <td><?= $no++ ?></td>

    <td><?= date('d M Y', strtotime($r['tanggal_kunjungan'])) ?></td>

    <td><?= (int)$r['jumlah_orang'] ?> org</td>

    <td><?= ($r['sewa_jeep'] === 'ya') ? 'Ya' : '-' ?></td>

    <td>
        <?= ($r['sewa_trail'] === 'ya')
            ? ((int)$r['jumlah_trail'] . ' unit')
            : '-' ?>
    </td>

    <td>
        Rp <?= number_format((int)$r['total_harga'], 0, ',', '.') ?>
    </td>

    <td>
        <span class="badge <?= $badge ?>">
            <?= ucfirst($status) ?>
        </span>
    </td>

    <td>
        <?= date('d M Y H:i', strtotime($r['created_at'])) ?>
    </td>

    <!-- ================= AKSI ================= -->
    <td>
        <?php if ($status === 'pending'): ?>

            <a href="bayar_bromo.php?id=<?= $r['id'] ?>"
               class="btn btn-sm btn-success mb-1">
                Bayar
            </a>

            <a href="batal_bromo.php?id=<?= $r['id'] ?>"
               onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
               class="btn btn-sm btn-danger">
                Batal
            </a>

        <?php elseif ($status === 'menunggu_verifikasi'): ?>

            <span class="text-muted small">
                Menunggu verifikasi admin
            </span>

        <?php elseif ($status === 'cancelled'): ?>

            <span class="text-danger fw-bold">
                ✕ Dibatalkan
            </span>

        <?php else: ?>

            <span class="text-success fw-bold">
                ✓ Lunas
            </span>

        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<?php endif; ?>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>
