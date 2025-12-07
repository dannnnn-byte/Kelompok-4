<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
?>

<div class="container py-5">

    <!-- ================= HEADER DASHBOARD ================= -->
    <div class="d-flex align-items-center gap-3 mb-4">
<img src="../img/jawatrip1.png" alt="JawaTrip Logo" style="width:70px;">


        <div>
            <h2 class="mb-0">Dashboard Admin</h2>
            <p class="mb-0">
                Selamat datang, <strong><?= htmlspecialchars($nama_admin); ?></strong>
            </p>
        </div>
    </div>

    <!-- ================= STATISTIK ================= -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5>Total Pemesanan Wisata</h5>
                    <p class="display-6 mb-0"><?= $wisata_count; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5>Total Pemesanan Hotel</h5>
                    <p class="display-6 mb-0"><?= $hotel_count; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= RIWAYAT WISATA ================= -->
    <h4 class="mb-3">Riwayat Pemesanan Wisata</h4>
    <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped align-middle">
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

                if (mysqli_num_rows($q) > 0) {
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
                } else {
                    echo "<tr>
                        <td colspan='7' class='text-center text-muted'>
                            Belum ada pemesanan wisata
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr>
                    <td colspan='7' class='text-center text-danger'>
                        Tabel pemesanan_wisata belum tersedia
                    </td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- ================= RIWAYAT HOTEL ================= -->
    <h4 class="mb-3">Riwayat Pemesanan Hotel</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
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

                if (mysqli_num_rows($q) > 0) {
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
                } else {
                    echo "<tr>
                        <td colspan='9' class='text-center text-muted'>
                            Belum ada pemesanan hotel
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr>
                    <td colspan='9' class='text-center text-danger'>
                        Tabel pemesanan_hotel belum tersedia
                    </td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
</div>

<?php include '../includes/footer.php'; ?>
