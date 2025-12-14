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
        + Tambah Destinasi
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
      <div class="card h-100 shadow-sm">
        <img src="<?= $d[1] ?>" class="card-img-top" style="height:250px;object-fit:cover;">
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

  <!-- ========================= -->
  <!-- ✅ PAKET DARI DATABASE -->
  <!-- ========================= -->
  <h4 class="fw-bold mb-3">Destinasi Tambahan</h4>

  <div class="row g-4">
    <?php
    $data = mysqli_query($conn, "SELECT * FROM destinasi");
    while ($d = mysqli_fetch_assoc($data)):
    ?>
    <div class="col-md-3">
      <div class="card h-100 shadow-sm">
        <img src="<?= $d['gambar'] ?>" class="card-img-top" style="height:250px;object-fit:cover;">
        <div class="card-body text-center" style="background:#145C43;color:white;">
          <h5 class="fw-bold"><?= $d['kota'] ?></h5>

          <a href="paket.php?kota=<?= $d['kota'] ?>"
             class="btn w-100 mt-2 fw-bold"
             style="background:#CDAA7D;color:#145C43;">
            Lihat Paket
          </a>

          <!-- ✅ CRUD KHUSUS ADMIN -->
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <div class="d-flex gap-2 mt-3">
            <a href="admin/edit_destinasi.php?id=<?= $d['id'] ?>"
               class="btn btn-warning btn-sm w-50">Edit</a>

            <a href="admin/hapus_destinasi.php?id=<?= $d['id'] ?>"
               class="btn btn-danger btn-sm w-50"
               onclick="return confirm('Yakin hapus destinasi ini?')">
               Hapus
            </a>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
</div>
