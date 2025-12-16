<?php
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/dashboard_home.php';

// Ambil kota dari URL
$kota_terpilih = isset($_GET['kota']) ? $_GET['kota'] : '';

if ($kota_terpilih == '') {
    echo "<div class='container py-5'><div class='alert alert-danger'>Kota tidak ditemukan.</div></div>";
    include 'includes/footer.php';
    exit;
}

// ========================
// AMBIL DATA KOTA
// ========================
$qKota = mysqli_query($conn, "SELECT * FROM kota WHERE nama_kota='$kota_terpilih'");
$kota = mysqli_fetch_assoc($qKota);

if (!$kota) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Data kota tidak tersedia.</div></div>";
    include 'includes/footer.php';
    exit;
}

// ========================
// AMBIL PAKET WISATA
// ========================
$qPaket = mysqli_query($conn, "
    SELECT * FROM paket_wisata 
    WHERE id_kota='{$kota['id_kota']}'
");
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-center flex-grow-1">
            PAKET WISATA <?= strtoupper($kota_terpilih); ?>
        </h2>
        
        <!-- ✅ Tombol tambah hanya admin -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin/tambah_wisata.php?id_kota=<?= $kota['id_kota'] ?>" class="btn btn-success fw-bold ms-3">
                <i class="bi bi-plus-lg"></i> Paket
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php if (mysqli_num_rows($qPaket) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($qPaket)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-lg rounded-4 overflow-hidden h-100 position-relative" style="border: none; transition: transform 0.3s;">
                        <img src="img/<?= $p['gambar_paket']; ?>" class="card-img-top"
                             style="height:300px; object-fit:cover;">

                        <!-- ✅ Tombol CRUD untuk Admin -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="position-absolute top-0 end-0 p-3" style="z-index: 10; background: rgba(0,0,0,0.6); border-radius: 0 0 0 8px;">
                            <div class="d-flex gap-2">
                                <a href="admin/edit_wisata.php?id=<?= $p['id_paket'] ?>" 
                                   class="btn btn-warning btn-sm fw-bold"
                                   title="Edit Paket"
                                   style="padding: 6px 10px;">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form method="POST" action="admin/delete_wisata.php" class="d-inline" 
                                      onsubmit="return confirm('Yakin hapus paket <?= htmlspecialchars($p['nama_paket']) ?>?')">
                                    <input type="hidden" name="id_paket" value="<?= $p['id_paket'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm fw-bold"
                                            title="Hapus Paket"
                                            style="padding: 6px 10px;">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="card-body text-white d-flex flex-column"
                             style="background:#145C43;">
                            <h5 class="fw-bold"><?= $p['nama_paket']; ?></h5>

                            <p class="mb-0 mt-3" style="font-size:13px;">Start from</p>
                            <h4 class="fw-bold">
                                Rp<?= number_format($p['harga_per_pax'], 0, ',', '.'); ?>
                            </h4>

                            <a href="wisatamalang.php?id=<?= $p['id_paket']; ?>"
                               class="btn mt-auto fw-bold"
                               style="background:#CDAA7D; color:#145C43;">
                               Lihat Paket
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    Belum ada paket wisata untuk kota ini
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
