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
    p.status_bayar AS status_bayar_pemesanan,
    COALESCE(p.tanggal_pesan, NOW()) AS tanggal_pesan,
    COALESCE(pay.tanggal_bayar, p.tanggal_pesan) AS waktu_pesan_display,
    pk.nama_paket,
    k.nama_kota,
    COALESCE((
        SELECT nama_lengkap FROM penumpang 
        WHERE id_pemesanan = p.id_pemesanan 
        ORDER BY id_penumpang ASC LIMIT 1
    ), u.nama_lengkap, 'Guest') AS nama_pemesan,
    pay.status_bayar AS status_bayar_pembayaran,
    pay.tanggal_bayar,
    pay.tanggal_konfirmasi
FROM pemesanan p
JOIN paket_wisata pk ON p.id_paket = pk.id_paket
JOIN kota k ON pk.id_kota = k.id_kota
LEFT JOIN users u ON p.id_user = u.id_user
LEFT JOIN (
    SELECT t.kode_booking, t.status_bayar, t.tanggal_bayar, t.tanggal_konfirmasi
    FROM pembayaran t
    JOIN (
        SELECT kode_booking, MAX(tanggal_bayar) AS max_t
        FROM pembayaran
        GROUP BY kode_booking
    ) m ON t.kode_booking = m.kode_booking AND t.tanggal_bayar = m.max_t
) pay ON pay.kode_booking = p.kode_booking
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

    <!-- ================= TABEL PEMESANAN WISATA ================= -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-map-fill" style="font-size: 1.5rem; color: #145C43;"></i>
            <h4 class="fw-bold mb-0">Riwayat Pemesanan Paket Wisata</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode Booking</th>
                        <th>Nama Pemesan</th>
                        <th>Paket</th>
                        <th>Kota</th>
                        <th>Tanggal Tour</th>
                        <th>Peserta</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Bukti Pembayaran</th>
                        <th>Aksi</th>
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
                        <?php 
                        $statusPemesanan = $row['status_bayar_pemesanan'];
                        $statusPembayaran = $row['status_bayar_pembayaran'];
                        $isLunas = ($statusPemesanan === 'lunas') || ($statusPembayaran === 'lunas');
                        ?>
                        <span class="badge <?= $isLunas ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= $isLunas ? '✓ Lunas' : ($statusPembayaran ?: ($statusPemesanan ?: 'pending')) ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        // Query bukti pembayaran dari tabel pembayaran
                        $proofQuery = "SELECT bukti_bayar FROM pembayaran WHERE kode_booking = ? LIMIT 1";
                        $proofStmt = $conn->prepare($proofQuery);
                        if ($proofStmt) {
                            $proofStmt->bind_param('s', $row['kode_booking']);
                            $proofStmt->execute();
                            $proofRes = $proofStmt->get_result();
                            $proofRow = $proofRes->fetch_assoc();
                            $buktiFile = $proofRow['bukti_bayar'] ?? null;
                            $proofStmt->close();
                        }
                        ?>
                        <?php if ($buktiFile): ?>
                            <a href="../uploads/bukti_bayar/<?= urlencode($buktiFile) ?>" 
                               class="btn btn-sm btn-info" 
                               target="_blank"
                               title="Lihat Bukti Pembayaran">
                                <i class="bi bi-image"></i> Lihat
                            </a>
                        <?php else: ?>
                            <span class="text-muted text-center" style="display: inline-block; width: 100%;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <!-- View Passengers Button -->
                            <button type="button" 
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#pesertaModal<?= $row['id_pemesanan'] ?>"
                                    title="Lihat Daftar Peserta">
                                <i class="bi bi-people"></i> Lihat Peserta
                            </button>
                            
                            <?php if ($isLunas): ?>
                                <a href="../payment_success.php?kode=<?= urlencode($row['kode_booking']) ?>" class="btn btn-sm btn-outline-success" title="Lihat Invoice Pembayaran">
                                    <i class="bi bi-receipt"></i> Invoice
                                </a>
                                <?php if ($row['tanggal_konfirmasi']): ?>
                                    <small class="text-success">
                                        <i class="bi bi-calendar-check"></i> <?= date('d M Y', strtotime($row['tanggal_konfirmasi'])) ?>
                                    </small>
                                <?php endif; ?>
                            <?php elseif (in_array(($statusPembayaran ?? 'pending'), ['menunggu_verifikasi','pending'])): ?>
                                <form method="POST" action="verify_payment.php" class="d-inline">
                                    <input type="hidden" name="kode_booking" value="<?= htmlspecialchars($row['kode_booking']) ?>">
                                    <button type="submit" class="btn btn-sm btn-success fw-bold" 
                                            onclick="return confirm('Verifikasi pembayaran untuk <?= htmlspecialchars($row['kode_booking']) ?>?');"
                                            style="padding: 6px 12px; border-radius: 5px;">
                                        <i class="bi bi-check-circle-fill"></i> Verifikasi
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                            <!-- Edit & Delete Buttons -->
                            <div class="d-flex gap-2" style="margin-top: 8px;">
                                <a href="edit_pemesanan.php?id=<?= urlencode($row['id_pemesanan']) ?>" 
                                   class="btn btn-sm btn-warning fw-bold" 
                                   title="Edit Pemesanan"
                                   style="padding: 6px 12px; border-radius: 5px;">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form method="POST" action="delete_pemesanan.php" class="d-inline">
                                    <input type="hidden" name="id_pemesanan" value="<?= htmlspecialchars($row['id_pemesanan']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger fw-bold" 
                                            onclick="return confirm('Yakin hapus pemesanan <?= htmlspecialchars($row['kode_booking']) ?>? Data penumpang juga akan terhapus.');"
                                            style="padding: 6px 12px; border-radius: 5px;">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
<td><?= date('d M Y H:i', strtotime($row['waktu_pesan_display'])) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center text-muted">
                        Belum ada pemesanan
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- ================= MODALS PESERTA ================= -->
    <?php 
    // Reset pointer untuk query peserta
    mysqli_data_seek($data_pemesanan, 0);
    while ($row = mysqli_fetch_assoc($data_pemesanan)): 
        $id_pemesanan = $row['id_pemesanan'];
        
        // Query peserta untuk booking ini
        $query_peserta = "SELECT * FROM penumpang WHERE id_pemesanan = '$id_pemesanan' ORDER BY id_penumpang ASC";
        $result_peserta = mysqli_query($conn, $query_peserta);
    ?>
    <div class="modal fade" id="pesertaModal<?= $id_pemesanan ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-people"></i> Daftar Peserta Pemesanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-2"><strong>Kode Booking:</strong> <?= htmlspecialchars($row['kode_booking']) ?></p>
                        <p class="mb-2"><strong>Paket:</strong> <?= htmlspecialchars($row['nama_paket']) ?></p>
                        <p class="mb-0"><strong>Tanggal Tour:</strong> <?= date('d M Y', strtotime($row['tgl_tour'])) ?></p>
                    </div>
                    
                    <?php if (mysqli_num_rows($result_peserta) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>No. Telepon</th>
                                    <th>Tipe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($peserta = mysqli_fetch_assoc($result_peserta)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($peserta['nama_lengkap']) ?></strong></td>
                                    <td><?= htmlspecialchars($peserta['email']) ?></td>
                                    <td><?= htmlspecialchars($peserta['no_telepon']) ?></td>
                                    <td>
                                        <span class="badge <?= $peserta['tipe_penumpang'] === 'Dewasa' ? 'bg-success' : 'bg-info' ?>">
                                            <?= ucfirst($peserta['tipe_penumpang']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Detail Peserta Lengkap -->
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Detail Lengkap Peserta</h6>
                        <?php 
                        // Reset query untuk menampilkan detail lengkap
                        mysqli_data_seek($result_peserta, 0);
                        $no = 1;
                        while ($peserta = mysqli_fetch_assoc($result_peserta)): 
                        ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-person-badge"></i> Peserta <?= $no ?> - 
                                    <span class="badge <?= $peserta['tipe_penumpang'] === 'Dewasa' ? 'bg-success' : 'bg-info' ?>">
                                        <?= ucfirst($peserta['tipe_penumpang']) ?>
                                    </span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong>Nama:</strong><br>
                                            <?= htmlspecialchars($peserta['nama_lengkap']) ?>
                                        </p>
                                        <p class="mb-2">
                                            <strong>Email:</strong><br>
                                            <?= htmlspecialchars($peserta['email']) ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong>No. Telepon:</strong><br>
                                            <?= htmlspecialchars($peserta['no_telepon']) ?>
                                        </p>
                                        <p class="mb-2">
                                            <strong>No. Identitas:</strong><br>
                                            <?= htmlspecialchars($peserta['no_identitas']) ?>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    <strong>Alamat:</strong><br>
                                    <small><?= htmlspecialchars($peserta['alamat']) ?></small>
                                </p>
                            </div>
                        </div>
                        <?php $no++; endwhile; ?>
                    </div>
                    
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Tidak ada data peserta untuk pemesanan ini
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    </div>

    <!-- ================= TABEL PEMESANAN BROMO ================= -->
    <div class="mt-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-mountain" style="font-size: 1.5rem; color: #dc2626;"></i>
            <h4 class="fw-bold mb-0">Riwayat Pemesanan Gunung Bromo</h4>
        </div>

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
            backgroundColor: ['#0d6efd']
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});
</script>

<?php include 'footer_admin.php'; ?>
