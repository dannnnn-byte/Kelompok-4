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

/* ================= NAMA ADMIN ================= */
$nama_admin = $_SESSION['nama'] ?? $_SESSION['email'] ?? 'Admin';

/* ================= STATISTIK PEMESANAN ================= */
$q_count = mysqli_query($conn, "SELECT COUNT(*) total FROM pemesanan");
$total_pemesanan = mysqli_fetch_assoc($q_count)['total'] ?? 0;

$q_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) total FROM pemesanan");
$total_pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

/* ================= DATA PEMESANAN ================= */
$query = "
SELECT 
    p.id_pemesanan,
    p.kode_booking,
    p.tgl_tour,
    p.jumlah_peserta,
    p.total_bayar,
    p.status_bayar,
    COALESCE(p.tanggal_pesan, NOW()) AS tanggal_pesan,
    pk.nama_paket,
    k.nama_kota,
    COALESCE(u.nama_lengkap, 'Guest') AS nama_pemesan
FROM pemesanan p
JOIN paket_wisata pk ON p.id_paket = pk.id_paket
JOIN kota k ON pk.id_kota = k.id_kota
LEFT JOIN users u ON p.id_user = u.id_user
ORDER BY p.id_pemesanan DESC
";




$data_pemesanan = mysqli_query($conn, $query);
?>

<div class="container py-5">

    <!-- ================= HEADER ================= -->
    <div class="d-flex align-items-center gap-3 mb-5">
        <img src="../img/jawatrip1.png" style="width:65px;">
        <div>
            <h2 class="fw-bold mb-0">Dashboard Admin</h2>
            <p class="text-muted mb-0">
                Selamat datang, <strong><?= htmlspecialchars($nama_admin); ?></strong>
            </p>
        </div>
    </div>

    <!-- ================= STATISTIK ================= -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow h-100">
                <div class="card-body">
                    <h6>Total Pemesanan</h6>
                    <h1 class="fw-bold"><?= $total_pemesanan ?></h1>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body">
                    <h6>Total Pendapatan</h6>
                    <h1 class="fw-bold">
                        Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= GRAFIK ================= -->
    <div class="card shadow mb-5">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Grafik Pemesanan</h5>
            <canvas id="chartPemesanan" height="100"></canvas>
        </div>
    </div>

    <!-- ================= TABEL PEMESANAN ================= -->
    <h4 class="fw-bold mb-3">Riwayat Pemesanan Wisata</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode Booking</th>
                    <th>Nama Pemesan</th>
                    <th>Paket</th>
                    <th>Kota</th>
                    <th>Tanggal Tour</th>
                    <th>Peserta</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Waktu Pesan</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data_pemesanan) > 0): ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($data_pemesanan)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $row['kode_booking'] ?></strong></td>
<td><?= htmlspecialchars($row['nama_pemesan']) ?></td>

                    <td><?= $row['nama_paket'] ?></td>
                    <td><?= $row['nama_kota'] ?></td>
                    <td><?= date('d M Y', strtotime($row['tgl_tour'])) ?></td>
                    <td><?= $row['jumlah_peserta'] ?> Orang</td>
                    <td>
                        Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <span class="badge 
                            <?= $row['status_bayar'] == 'paid' ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= ucfirst($row['status_bayar']) ?>
                        </span>
                    </td>
<td><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center text-muted">
                        Belum ada pemesanan
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ================= CHART JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartPemesanan'), {
    type: 'bar',
    data: {
        labels: ['Pemesanan'],
        datasets: [{
            data: [<?= $total_pemesanan ?>],
            backgroundColor: ['#198754']
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});
</script>

<?php include 'footer_admin.php'; ?>
