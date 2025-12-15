<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

// Ambil riwayat pemesanan user
$query = "
SELECT p.id_pemesanan, p.kode_booking, p.tgl_tour, p.jumlah_peserta,
         p.total_harga, p.total_bayar, p.status_bayar, p.tanggal_pesan,
         pk.nama_paket, k.nama_kota
FROM pemesanan p
JOIN paket_wisata pk ON p.id_paket = pk.id_paket
JOIN kota k ON pk.id_kota = k.id_kota
WHERE p.id_user = ?
    OR EXISTS (
         SELECT 1 FROM penumpang pe
         WHERE pe.id_pemesanan = p.id_pemesanan
            AND pe.email = ?
    )
ORDER BY p.tanggal_pesan DESC, p.id_pemesanan DESC";

$rows = [];
$stmt = $conn->prepare($query);
if ($stmt) {
    $userEmail = $_SESSION['email'] ?? '';
    $stmt->bind_param('is', $userId, $userEmail);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($res && ($row = $res->fetch_assoc())) {
        $rows[] = $row;
    }
    $stmt->close();
}
?>

<link rel="stylesheet" href="assets/dashboard_home.css">

<div class="container py-5">
    <h3 class="fw-bold mb-4">Riwayat Pesanan</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode Booking</th>
                    <th>Paket</th>
                    <th>Kota</th>
                    <th>Tanggal Tour</th>
                    <th>Peserta</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php $no = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($r['kode_booking']) ?></strong></td>
                        <td><?= htmlspecialchars($r['nama_paket']) ?></td>
                        <td><?= htmlspecialchars($r['nama_kota']) ?></td>
                        <td><?= $r['tgl_tour'] ? date('d M Y', strtotime($r['tgl_tour'])) : '-' ?></td>
                        <td><?= (int)$r['jumlah_peserta'] ?> Orang</td>
                        <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <?php $isLunas = ($r['status_bayar'] === 'lunas'); ?>
                            <span class="badge <?= $isLunas ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $isLunas ? 'Lunas' : ($r['status_bayar'] ?: 'pending') ?>
                            </span>
                        </td>
                        <td>
                            <?php $isLunas = ($r['status_bayar'] === 'lunas'); ?>
                            <?php if (!$isLunas): ?>
                                <a class="btn btn-sm btn-primary" href="pembayaran.php?kode=<?= urlencode($r['kode_booking']) ?>">
                                    <i class="bi bi-credit-card"></i> Pembayaran
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-sm btn-secondary" href="payment_success.php?kode=<?= urlencode($r['kode_booking']) ?>">
                                <i class="bi bi-receipt"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Belum ada riwayat pemesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
