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

/* ================= FUNCTION CEK TABEL ================= */
function cekTabel($conn, $table) {
    $cek = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return mysqli_num_rows($cek) > 0;
}

/* ================= STATISTIK ================= */
$wisata_count = 0;
$hotel_count  = 0;

if (cekTabel($conn, 'pemesanan_wisata')) {
    $q = mysqli_query($conn, "SELECT COUNT(*) total FROM pemesanan_wisata");
    $wisata_count = mysqli_fetch_assoc($q)['total'];
}

if (cekTabel($conn, 'pemesanan_hotel')) {
    $q = mysqli_query($conn, "SELECT COUNT(*) total FROM pemesanan_hotel");
    $hotel_count = mysqli_fetch_assoc($q)['total'];
}

/* ================= LAPORAN KEUANGAN ================= */
$total_wisata = 0;
$total_hotel  = 0;

if (cekTabel($conn, 'pemesanan_wisata')) {
    $q = mysqli_query($conn, "SELECT SUM(total_harga) total FROM pemesanan_wisata");
    $total_wisata = mysqli_fetch_assoc($q)['total'] ?? 0;
}

if (cekTabel($conn, 'pemesanan_hotel')) {
    $q = mysqli_query($conn, "SELECT SUM(total_harga) total FROM pemesanan_hotel");
    $total_hotel = mysqli_fetch_assoc($q)['total'] ?? 0;
}

$total_pendapatan = $total_wisata + $total_hotel;
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
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow h-100">
                <div class="card-body">
                    <h6>Total Pemesanan Wisata</h6>
                    <h1 class="fw-bold"><?= $wisata_count ?></h1>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body">
                    <h6>Total Pemesanan Hotel</h6>
                    <h1 class="fw-bold"><?= $hotel_count ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= LAPORAN KEUANGAN ================= -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pendapatan Wisata</h6>
                    <h4 class="fw-bold text-primary">
                        Rp <?= number_format($total_wisata, 0, ',', '.') ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pendapatan Hotel</h6>
                    <h4 class="fw-bold text-success">
                        Rp <?= number_format($total_hotel, 0, ',', '.') ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white shadow border-0">
                <div class="card-body">
                    <h6>Total Pendapatan</h6>
                    <h4 class="fw-bold">
                        Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                    </h4>
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

    <!-- ================= RIWAYAT WISATA ================= -->
    <h4 class="fw-bold mb-3">Riwayat Pemesanan Wisata</h4>
    <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Wisata</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Transportasi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (cekTabel($conn, 'pemesanan_wisata')) {
                $no = 1;
                $q = mysqli_query($conn, "SELECT * FROM pemesanan_wisata ORDER BY created_at DESC");
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>
                        <td>$no</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['wisata']}</td>
                        <td>{$row['tanggal']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>{$row['transportasi']}</td>
                        <td>{$row['created_at']}</td>
                    </tr>";
                    $no++;
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- ================= RIWAYAT HOTEL ================= -->
    <h4 class="fw-bold mb-3">Riwayat Pemesanan Hotel</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Hotel</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Kamar</th>
                    <th>Orang</th>
                    <th>Transportasi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (cekTabel($conn, 'pemesanan_hotel')) {
                $no = 1;
                $q = mysqli_query($conn, "SELECT * FROM pemesanan_hotel ORDER BY created_at DESC");
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>
                        <td>$no</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['hotel']}</td>
                        <td>{$row['tanggal_checkin']}</td>
                        <td>{$row['tanggal_checkout']}</td>
                        <td>{$row['jumlah_kamar']}</td>
                        <td>{$row['jumlah_orang']}</td>
                        <td>{$row['transportasi']}</td>
                        <td>{$row['created_at']}</td>
                    </tr>";
                    $no++;
                }
            }
            ?>
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
        labels: ['Wisata', 'Hotel'],
        datasets: [{
            data: [<?= $wisata_count ?>, <?= $hotel_count ?>],
            backgroundColor: ['#0d6efd','#198754']
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});
</script>

<?php include 'footer_admin.php'; ?>
