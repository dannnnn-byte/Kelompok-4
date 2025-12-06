<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Pastikan session sudah aktif
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center text-white fs-4 ms-5" href="index.php">
      <img src="img/jawatrip1.png" alt="logo" class="logo me-2">
      JawaTrip
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav text-center">

        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="pesan.php">Book Ticket</a></li>

        <?php if (isset($_SESSION['username'])): ?>
          <!-- Tampilkan username + dropdown logout -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white fw-semibold px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
              👤 <?= htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profil.php">Profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger fw-bold" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="login.php">Login</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
