<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!-- Sidebar toggle and markup (partial include) -->

<!-- Sidebar toggle -->
<input type="checkbox" id="toggle-menu">

<!-- Hamburger button -->
<label for="toggle-menu" class="menu-icon">
  <div></div>
  <div></div>
  <div></div>
</label>

<!-- Sidebar -->
<nav class="side-menu">
  <label for="toggle-menu" class="close-btn">&times;</label>

  <div class="logo-title">
      <img src="img/jawatrip1.png" alt="Logo JawaTrip">
      <h3>JawaTrip</h3>
  </div>

  <ul>
      <li><a href="index.php">Beranda</a></li>

      <div class="accordion" id="rekWisata">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
    <button class="accordion-button collapsed" type="button"
        data-bs-toggle="collapse"
        data-bs-target="#collapseOne"
        style="font-weight: bold;">
    Rekomendasi Wisata
</button>

    </h2>

    <div id="collapseOne" 
         class="accordion-collapse collapse" 
         data-bs-parent="#rekWisata">
      <div class="accordion-body">

        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" 
                  type="button" 
                  data-bs-toggle="dropdown">
            Pilih Wisata
          </button>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="destinasi/bromo.php">Bromo</a></li>
            <li><a class="dropdown-item" href="destinasi/tumpaksewu.php">Tumpak Sewu</a></li>
            <li><a class="dropdown-item" href="destinasi/kawahijen.php">Kawah Ijen</a></li>
            <li><a class="dropdown-item" href="destinasi/museumangkut.php">Museum Angkut</a></li>
            <li><a class="dropdown-item" href="destinasi/wbl.php">Wisata Bahari Lamongan</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</div>

      <li><a href="wisata.php">Paket Wisata</a></li>
      <li><a href="hotel.php">Hotel</a></li>
      <li><a href="transportasi.php">Transportasi</a></li>
      <li><a href="pesan.php">Pesan Tiket</a></li>
      <li><a href="login.php">Login</a></li>
  </ul>
</nav>
