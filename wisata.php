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

  </div>

  <!-- ===================== -->
  <!-- ✅ DESTINASI DINAMIS -->
  <!-- ===================== -->
  <?php
    $q_destinasi = mysqli_query($conn, "SELECT nama_kota, gambar_kota FROM kota ORDER BY id_kota ASC");
  ?>
  <div class="row g-4 mb-5">
    <?php if ($q_destinasi && mysqli_num_rows($q_destinasi) > 0): ?>
        <?php while ($d = mysqli_fetch_assoc($q_destinasi)): 
            $img = !empty($d['gambar_kota']) ? 'img/' . $d['gambar_kota'] : 'img/default-destinasi.jpg';
        ?>
        <div class="col-md-3">
          <div class="card h-100 shadow-sm position-relative overflow-hidden" style="border: none;">
            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:250px;object-fit:cover;">
            
            <div class="card-body text-center" style="background:#145C43;color:white;">
              <h5 class="fw-bold"><?= htmlspecialchars($d['nama_kota']) ?></h5>

              <a href="wisata_kota.php?kota=<?= urlencode($d['nama_kota']) ?>"
                 class="btn w-100 mt-2 fw-bold"
                 style="background:#CDAA7D;color:#145C43;">
                Lihat Paket
              </a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info d-flex align-items-center justify-content-between">
          <div>
            <i class="bi bi-info-circle"></i> Belum ada data kota di database.
          </div>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="admin/tambah_kota.php" class="btn btn-success btn-sm fw-bold">
            <i class="bi bi-plus-lg"></i> Tambah Kota
          </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>



<?php include 'includes/footer.php'; ?>
</div>
