<style>
  /* Ensure admin sidebar sits below top navbar dropdown */
  .admin-sidebar-navbar { position: relative; z-index: 100; }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark admin-sidebar-navbar">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">
      <i class="bi bi-speedometer2"></i> Admin JawaTrip
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="dashboard.php" class="nav-link">
          <i class="bi bi-house"></i> Dashboard
        </a></li>
        <li class="nav-item">
          <a class="nav-link"
              href="<?= $basePath ?>admin/user_list.php">
              <i class="bi bi-people"></i>
              Manajemen Pengguna
          </a>
        </li>
        <li class="nav-item"><a href="wisata_list.php" class="nav-link">
          <i class="bi bi-map"></i> Paket Wisata
        </a></li>
        <li class="nav-item"><a href="kota_list.php" class="nav-link">
          <i class="bi bi-geo-alt"></i> Kota / Destinasi
        </a></li>
        <li class="nav-item"><a href="hotel_list.php" class="nav-link">
          <i class="bi bi-building"></i> Hotel
        </a></li>
        <li class="nav-item"><a href="transportasi_list.php" class="nav-link">
          <i class="bi bi-truck"></i> Transportasi
        </a></li>
        <li class="nav-item"><a href="analytics.php" class="nav-link">
          <i class="bi bi-graph-up"></i> Analytics
        </a></li>
        <li class="nav-item"><a href="promo_management.php" class="nav-link">
          <i class="bi bi-gift"></i> Promo
        </a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link text-danger">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a></li>
      </ul>
    </div>
  </div>
</nav>

