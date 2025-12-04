<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <strong><?php echo $_SESSION['admin']; ?></strong>!</p>

    <?php
    // Hitung jumlah pemesanan Wisata
    $wisata_count = 0;
    $check_wisata = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan_wisata'");
    if(mysqli_num_rows($check_wisata) > 0){
        $result_wisata = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pemesanan_wisata");
        $row = mysqli_fetch_assoc($result_wisata);
        $wisata_count = $row['total'];
    }

    // Hitung jumlah pemesanan Hotel
    $hotel_count = 0;
    $check_hotel = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan_hotel'");
    if(mysqli_num_rows($check_hotel) > 0){
        $result_hotel = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pemesanan_hotel");
        $row = mysqli_fetch_assoc($result_hotel);
        $hotel_count = $row['total'];
    }
    ?>

    <!-- Statistik singkat -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5>Total Pemesanan Wisata</h5>
                    <p class="display-6"><?php echo $wisata_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5>Total Pemesanan Hotel</h5>
                    <p class="display-6"><?php echo $hotel_count; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pemesanan Wisata -->
    <h4>Riwayat Pemesanan Wisata</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Wisata</th>
                    <th>Tanggal</th>
                    <th>Jumlah Orang</th>
                    <th>Transportasi</th>
                    <th>Waktu Pemesanan</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(mysqli_num_rows($check_wisata) > 0){
                $no = 1;
                $result = mysqli_query($conn, "SELECT * FROM pemesanan_wisata ORDER BY created_at DESC");
                while($row = mysqli_fetch_assoc($result)){
                    echo "<tr>
                            <td>{$no}</td>
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
                echo "<tr><td colspan='7' class='text-center'>Belum ada data pemesanan wisata.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Riwayat Pemesanan Hotel -->
    <h4>Riwayat Pemesanan Hotel</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Hotel</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Jumlah Kamar</th>
                    <th>Jumlah Orang</th>
                    <th>Transportasi</th>
                    <th>Waktu Pemesanan</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(mysqli_num_rows($check_hotel) > 0){
                $no = 1;
                $result = mysqli_query($conn, "SELECT * FROM pemesanan_hotel ORDER BY created_at DESC");
                while($row = mysqli_fetch_assoc($result)){
                    echo "<tr>
                            <td>{$no}</td>
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
                echo "<tr><td colspan='9' class='text-center'>Belum ada data pemesanan hotel.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
</div>

<?php include '../includes/footer.php'; ?>