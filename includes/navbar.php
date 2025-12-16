<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLogin = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);

$isAdmin = (
    isset($_SESSION['admin_id']) ||
    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')
);

$username =
    $_SESSION['username']
    ?? $_SESSION['admin_username']
    ?? 'User';


// ================= AUTO PATH LOGO =================
$basePath = (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? '../' : '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm py-3">
  <div class="container">

    <a class="navbar-brand d-flex align-items-center text-white fs-4 ms-5"
       href="<?= $basePath ?>index.php">

      <img src="<?= $basePath ?>img/jawatrip1.png"
           alt="logo"
           style="height:42px"
           class="me-2">

      JawaTrip
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav text-center">

        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-3"
             href="<?= $basePath ?>index.php">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-3"
             href="<?= $basePath ?>pesan.php">Book Ticket</a>
        </li>

          <?php if ($isLogin): ?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-white fw-semibold px-3"
       href="#"
       role="button"
       data-bs-toggle="dropdown">

      👤 <?= htmlspecialchars($username) ?>
      <?php if ($isAdmin): ?>
        <span class="badge bg-warning text-dark ms-1">ADMIN</span>
      <?php endif; ?>
    </a>

    <ul class="dropdown-menu dropdown-menu-end">

      <!-- MENU ADMIN -->
      <?php if ($isAdmin): ?>
        <li>
          <a class="dropdown-item"
             href="<?= $basePath ?>admin/dashboard.php">
             Dashboard Admin
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>

      <!-- MENU USER -->
      <?php else: ?>
        <li>
          <a class="dropdown-item"
             href="<?= $basePath ?>profil.php">
             Profil Saya
          </a>
        </li>
        <li>
          <a class="dropdown-item"
             href="<?= $basePath ?>riwayat.php">
             Riwayat Pesanan
          </a>
        </li>
        <li>
          <a class="dropdown-item"
             href="<?= $basePath ?>riwayat_bromo.php">
             Riwayat Pesanan Bromo
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
      <?php endif; ?>

      <!-- LOGOUT (UNTUK SEMUA) -->
      <li>
        <a class="dropdown-item text-danger fw-bold"
           href="<?= $basePath ?>logout.php">
           Logout
        </a>
      </li>

    </ul>
</li>


<?php else: ?>
    <!-- BELUM LOGIN -->
    <li class="nav-item">
        <a class="nav-link text-white fw-semibold px-3"
           href="<?= $basePath ?>login.php">
           Login
        </a>
    </li>
<?php endif; ?>





      </ul>
    </div>
  </div>
</nav>
