<?php
session_start();
require_once 'koneksi.php';

include 'includes/header.php';
include 'includes/dashboard_home.php';
?>

<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Daftar Destinasi Wisata Jawa Timur</h2>

    <!-- ✅ Tombol tambah hanya admin -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <a href="admin/tambah_destinasi.php" class="btn btn-success fw-bold">
        <i class="bi bi-plus-lg"></i> Tambah Destinasi
      </a>
    <?php endif; ?>
  </div>

  <!-- ===================== -->
  <!-- ✅ PAKET LAMA (TETAP) -->
  <!-- ===================== -->
  <div class="row g-4 mb-5">
    <?php
    $destinasi_lama = [
      ["Batu", "img/batu.webp"],
      ["Mojokerto", "img/mojokerto.jpeg"],
      ["Sumenep", "img/sumenep.jpg"],
      ["Banyuwangi", "img/banyuwangi.jpeg"],
    ];

    foreach ($destinasi_lama as $d):
    ?>
    <div class="col-md-3">
      <div class="card h-100 shadow-sm position-relative overflow-hidden" style="border: none;">
        <img src="<?= $d[1] ?>" class="card-img-top" style="height:250px;object-fit:cover;">
        
        <!-- ✅ Tombol CRUD untuk Admin -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="position-absolute top-0 end-0 p-3" style="z-index: 10; background: rgba(0,0,0,0.5); border-radius: 0 0 0 8px;">
          <div class="d-flex gap-2">
            <a href="admin/edit_destinasi.php?kota=<?= urlencode($d[0]) ?>" 
               class="btn btn-warning btn-sm fw-bold"
               title="Edit Destinasi"
               style="padding: 6px 10px;">
              <i class="bi bi-pencil-square"></i> Edit
            </a>
            <form method="POST" action="admin/hapus_destinasi.php" class="d-inline" 
                  onsubmit="return confirm('Yakin hapus destinasi <?= htmlspecialchars($d[0]) ?>?')">
              <input type="hidden" name="kota" value="<?= htmlspecialchars($d[0]) ?>">
              <button type="submit" class="btn btn-danger btn-sm fw-bold"
                      title="Hapus Destinasi"
                      style="padding: 6px 10px;">
                <i class="bi bi-trash-fill"></i> Hapus
              </button>
            </form>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="card-body text-center" style="background:#145C43;color:white;">
          <h5 class="fw-bold"><?= $d[0] ?></h5>

          <a href="wisata_kota.php?kota=<?= $d[0] ?>"
             class="btn w-100 mt-2 fw-bold"
             style="background:#CDAA7D;color:#145C43;">
            Lihat Paket
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>



<?php include 'includes/footer.php'; ?>
</div>
