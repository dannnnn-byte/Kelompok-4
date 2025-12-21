<?php
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/dashboard_home.php';
// Komponen wishlist (JS + CSS button)
include 'includes/wishlist_button.php';

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
    </div>

    <div class="row g-4">
        <?php if (mysqli_num_rows($qPaket) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($qPaket)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-lg rounded-4 overflow-hidden h-100 position-relative" style="border: none; transition: transform 0.3s;">
                        <img src="img/<?= $p['gambar_paket']; ?>" class="card-img-top"
                             style="height:300px; object-fit:cover;">

                        <!-- Wishlist Button -->
                        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                        <button class="wishlist-btn" data-id="<?= $p['id_paket']; ?>"
                                onclick="toggleWishlist(<?= $p['id_paket']; ?>, this)">
                            <i class="bi bi-heart-fill"></i>
                        </button>
                        <script>checkWishlistStatus(<?= $p['id_paket']; ?>, document.currentScript.previousElementSibling);</script>
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
