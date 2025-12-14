<?php
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Ambil kota dari URL
$kota = isset($_GET['kota']) ? $_GET['kota'] : '';

if ($kota == '') {
    echo "<div class='container py-5 text-center'>
            <div class='alert alert-danger'>Kota tidak valid</div>
          </div>";
    include 'includes/footer.php';
    exit;
}

// Ambil paket wisata berdasarkan kota
$query = "SELECT p.* 
          FROM paket_wisata p
          JOIN kota k ON p.id_kota = k.id_kota
          WHERE k.nama_kota = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $kota);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5">
    <h2 class="fw-bold text-center mb-4">
        Paket Wisata <?= htmlspecialchars($kota) ?>
    </h2>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card shadow h-100">
                        <img src="img/<?= $row['gambar_paket'] ?>"
                             class="card-img-top"
                             style="height:250px; object-fit:cover;">

                        <div class="card-body text-white"
                             style="background:#145C43;">
                            <h5 class="fw-bold"><?= $row['nama_paket'] ?></h5>
                            <p class="mb-1">Mulai dari</p>
                            <h4 class="fw-bold">
                                Rp<?= number_format($row['harga_per_pax'],0,',','.') ?>
                            </h4>

                            <a href="wisatamalang.php?id=<?= $row['id_paket'] ?>"
                               class="btn w-100 mt-3 fw-bold"
                               style="background:#CDAA7D;color:#145C43;">
                               Detail Paket
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    Paket wisata untuk kota ini belum tersedia.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
