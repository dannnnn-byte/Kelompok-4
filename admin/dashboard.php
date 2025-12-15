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

/* ================= STATISTIK PEMESANAN BROMO ================= */
$q_bromo_count = mysqli_query($conn, "
    SELECT COUNT(*) total FROM pemesanan_bromo
");
$total_bromo = mysqli_fetch_assoc($q_bromo_count)['total'] ?? 0;

$q_bromo_pendapatan = mysqli_query($conn, "
    SELECT SUM(total_harga) total FROM pemesanan_bromo
    WHERE status = 'paid'
");
$total_pendapatan_bromo =
    mysqli_fetch_assoc($q_bromo_pendapatan)['total'] ?? 0;


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

/* ================= DATA PEMESANAN BROMO ================= */
$query_bromo = "
SELECT
    pb.id,
    pb.tanggal_kunjungan,
    pb.jumlah_orang,
    pb.sewa_jeep,
    pb.sewa_trail,
    pb.jumlah_trail,
    pb.total_harga,
    pb.status,
    pb.created_at,
    COALESCE(u.nama_lengkap, 'User') AS nama_user
FROM pemesanan_bromo pb
LEFT JOIN users u ON pb.user_id = u.id_user
ORDER BY pb.id DESC
";

$data_bromo = mysqli_query($conn, $query_bromo);
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

    <!-- TOTAL PEMESANAN WISATA -->
    <div class="col-md-6 col-lg-3">
        <div class="card bg-primary text-white shadow h-100">
            <div class="card-body">
                <h6>Total Pemesanan Wisata</h6>
                <h1 class="fw-bold"><?= $total_pemesanan ?></h1>
            </div>
        </div>
    </div>

    <!-- TOTAL PENDAPATAN WISATA -->
    <div class="col-md-6 col-lg-3">
        <div class="card bg-success text-white shadow h-100">
            <div class="card-body">
                <h6>Total Pendapatan Wisata</h6>
                <h1 class="fw-bold">
                    Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                </h1>
            </div>
        </div>
    </div>

    <!-- TOTAL PEMESANAN BROMO -->
    <div class="col-md-6 col-lg-3">
        <div class="card bg-warning text-dark shadow h-100">
            <div class="card-body">
                <h6>Total Pemesanan Bromo</h6>
                <h1 class="fw-bold"><?= $total_bromo ?></h1>
            </div>
        </div>
    </div>

    <!-- TOTAL PENDAPATAN BROMO -->
    <div class="col-md-6 col-lg-3">
        <div class="card bg-danger text-white shadow h-100">
            <div class="card-body">
                <h6>Total Pendapatan Bromo</h6>
                <h1 class="fw-bold">
                    Rp <?= number_format($total_pendapatan_bromo,0,',','.') ?>
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

<h4 class="fw-bold mt-5 mb-3">Riwayat Pemesanan Gunung Bromo</h4>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Nama User</th>
    <th>Tanggal Kunjungan</th>
    <th>Orang</th>
    <th>Jeep</th>
    <th>Trail</th>
    <th>Total</th>
    <th>Status</th>
    <th>Waktu Pesan</th>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($data_bromo) > 0): ?>
<?php $no=1; while($b=mysqli_fetch_assoc($data_bromo)): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($b['nama_user']) ?></td>
    <td><?= date('d M Y', strtotime($b['tanggal_kunjungan'])) ?></td>
    <td><?= $b['jumlah_orang'] ?> org</td>
    <td><?= $b['sewa_jeep']=='ya' ? 'Ya' : '-' ?></td>
    <td>
        <?= $b['sewa_trail']=='ya'
            ? $b['jumlah_trail'].' unit'
            : '-' ?>
    </td>
    <td>
        Rp <?= number_format($b['total_harga'],0,',','.') ?>
    </td>
    <td>
        <span class="badge 
        <?= $b['status']=='paid' ? 'bg-success' : 'bg-warning text-dark' ?>">
            <?= ucfirst($b['status']) ?>
        </span>
    </td>
    <td><?= date('d M Y H:i', strtotime($b['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="9" class="text-center text-muted">
        Belum ada pemesanan Bromo
    </td>
</tr>
<?php endif; ?>
</tbody>
</table>
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
