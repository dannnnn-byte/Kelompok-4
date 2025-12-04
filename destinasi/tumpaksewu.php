<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JawaTrip - Tumpak Sewu</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/dashboard_home.css">

<!-- Footer CSS -->
<link rel="stylesheet" href="../assets/footer2.css">

<!-- Favicon -->
<link rel="icon" type="image/png" href="../img/jawatrip1.png">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
/* Navbar z-index */
nav.navbar { z-index: 1100; }

/* Sidebar toggle */
#toggle-menu { display: none; }

/* 
NOTE: Sidebar CSS is now handled by includes/dashboard_home.css
Commenting out local sidebar styles to avoid conflicts

/* Tombol menu */
/*
.menu-icon {
  position: fixed;
  top: 20px;
  left: 20px;
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #00b050, #38d39f);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 6px;
  border-radius: 10px;
  cursor: pointer;
  z-index: 1200;
}
.menu-icon div { width:22px; height:3px; background:white; border-radius:3px; }

/* Sidebar */
/*
.side-menu {
  position: fixed;
  top:0;
  left:-280px;
  width:260px;
  height:100vh;
  background: rgba(255,255,255,0.9);
  backdrop-filter: blur(12px);
  padding: 30px 25px;
  box-shadow: 4px 0 15px rgba(0,0,0,0.1);
  transition: 0.4s ease;
  z-index: 1199;
  display: flex;
  flex-direction: column;
}
#toggle-menu:checked ~ .side-menu { left: 0; }

/* Close button */
/*
.close-btn {
  position: absolute;
  top: 18px;
  right: 20px;
  font-size: 32px;
  color: #00b050;
  cursor: pointer;
  transition: transform 0.3s ease;
}
.close-btn:hover { transform: rotate(90deg); color:#ff3333; }

/* Logo */
/*
.logo-title { display:flex; align-items:center; gap:12px; margin-bottom:40px; }
.logo-title img { width:45px; height:45px; border-radius:50%; border:2px solid #00b050; }
.logo-title h3 { font-size:22px; color:#00b050; font-weight:800; }

/* Menu items */
/*
.side-menu ul { list-style:none; padding:0; }
.side-menu ul li { margin:20px 0; }
.side-menu ul li a {
  text-decoration:none; color:#333; font-weight:600; display:flex; align-items:center; gap:10px;
  padding:8px 10px; border-radius:8px; transition: all 0.3s ease;
}
.side-menu ul li a:hover { background:#00b050; color:#fff; transform:translateX(5px); }
*/

/* Header utama */
.main-header h1 {
    font-size: 3rem;
    font-weight: 900;
    color: #ffffff;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
}

.main-header p {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    text-shadow: 1px 1px 6px rgba(0,0,0,0.5);
}

/* Main content */
.main-content {
    margin-left: 0;
    padding: 40px;
    transition: margin-left 0.3s ease;
    background-image: url('../img/tumpaksewu3.jpg'); 
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    color: #fff; 
    font-weight: 700;
}

/* Section Sejarah */
section.sejarah-bromo {
    position: relative;
    padding: 40px;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 30px;
}

section.sejarah-bromo::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-size: cover;
    background-position: center;
    filter: blur(10px) brightness(0.5);
    z-index: 0;
}

section.sejarah-bromo .content { position: relative; z-index: 1; color: #ffffff; font-weight: 700; }
section.sejarah-bromo h3 { color: #ffcc00; font-weight: 800; margin-bottom: 20px; }
section.sejarah-bromo img {
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
}

/* Map info card */
.map-info {
    background-color: rgba(0,0,0,0.6); 
    padding:15px 20px;
    border-radius:10px;
    box-shadow:0 4px 8px rgba(0,0,0,0.3);
    max-width:300px;
    margin-top:20px;
    color: #fff;
}

.map-info h4 { 
    color:#ffcc00; 
    margin-bottom:10px; 
}

.map-info p, .map-info a { 
    color: #fff; 
    font-weight: 700; 
}

.map-info a:hover { 
    color: #ffcc00; 
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm py-3">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center text-white fs-4" href="../index.php">
      <img src="../img/jawatrip1.png" alt="logo" class="logo me-2">
      JawaTrip
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav text-center">
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../wisata.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../hotel.php">Accommodation</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../transportasi.php">Transportation</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../pesan.php">Book Ticket</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Sidebar toggle -->
<input type="checkbox" id="toggle-menu">

<!-- Hamburger -->
<label for="toggle-menu" class="menu-icon">
  <div></div><div></div><div></div>
</label>

<!-- Sidebar -->
<nav class="side-menu">
  <label for="toggle-menu" class="close-btn">&times;</label>
  <div class="logo-title">
      <img src="../img/jawatrip1.png" alt="Logo JawaTrip">
      <h3>JawaTrip</h3>
  </div>
  <ul>
      <li><a href="../index.php">Beranda</a></li>
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
            <li><a class="dropdown-item" href="../destinasi/bromo.php">Bromo</a></li>
            <li><a class="dropdown-item" href="../destinasi/tumpaksewu.php">Tumpak Sewu</a></li>
            <li><a class="dropdown-item" href="../destinasi/kawahijen.php">Kawah Ijen</a></li>
            <li><a class="dropdown-item" href="../destinasi/museumangkut.php">Museum Angkut</a></li>
            <li><a class="dropdown-item" href="../destinasi/wbl.php">Wisata Bahari Lamongan</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</div>
      <li><a href="../wisata.php">Paket Wisata</a></li>
      <li><a href="../hotel.php">Hotel</a></li>
      <li><a href="../transportasi.php">Transportasi</a></li>
      <li><a href="../pesan.php">Pesan Tiket</a></li>
      <li><a href="../login.php">Login</a></li>
  </ul>
</nav>

<!-- Main Content -->
<main class="main-content">

 <header class="text-center mb-5 main-header">
  <h1>Tumpak Sewu</h1>
  <p>Air Terjun Terindah dan Megah di Jawa Timur</p>
</header>

<!-- Sejarah -->
<section class="sejarah-bromo mt-4">
  <div class="content row align-items-center">
    <div class="col-md-6">
      <h3>Sejarah & Keindahan Tumpak Sewu</h3>
      <p>
        Tumpak Sewu, dikenal juga dengan nama Coban Sewu, adalah air terjun megah di Lumajang
        yang terkenal karena bentuknya menyerupai tirai lebar dengan ratusan aliran air yang jatuh bersamaan.
        Nama “Tumpak Sewu” sendiri berarti “Seribu Air Terjun”, menggambarkan betapa banyaknya aliran air
        yang turun dari tebing setinggi sekitar 120 meter.
      </p>
      <p>
        Air terjun ini terbentuk dari aliran Sungai Glidik yang bersumber dari Gunung Semeru.
        Keindahannya mulai populer beberapa tahun terakhir dan kini menjadi destinasi favorit wisatawan lokal
        maupun mancanegara. Trek menuju dasar air terjun cukup menantang, namun pemandangan yang disuguhkan
        menjadi pengalaman luar biasa yang tidak terlupakan.
      </p>
    </div>
    <div class="col-md-6 text-center">
      <img src="../img/tumpaksewu2.webp" alt="Tumpak Sewu">
    </div>
  </div>
</section>

<div class="map-info">
    <h4>Tumpak Sewu</h4>
    <p>Pronojiwo, Lumajang</p>
    <p>Ketinggian: ± 120 meter</p>
    <p>Provinsi: Jawa Timur, Indonesia</p>
    <p><a href="https://www.google.com/maps/place/Tumpak+Sewu+waterfall+Lumajang/@-8.2283686,112.9169695,746m/data=!3m1!1e3!4m6!3m5!1s0x2dd613278111e811:0x250bde0ff61f8a46!8m2!3d-8.2283686!4d112.9195444!16s%2Fg%2F11y8n7grpb?entry=ttu&g_ep=EgoyMDI1MTExMi4wIKXMDSoASAFQAw%3D%3D" target="_blank">Buka Google Maps</a></p>
</div>


<div id="map"></div>

</main>

<!-- Footer -->
<footer class="modern-footer">
  <div class="footer-container">
      <div class="footer-brand">
          <img src="../img/jawatrip1.png" alt="JawaTrip Logo" class="footer-logo"> 
          <h2>JawaTrip</h2>
          <p>Partner perjalanan terbaik untuk menjelajah Jawa Timur - Indonesia.</p>
          </div>

      <div class="footer-links">
          <h4>Layanan</h4>
          <a href="#">Paket Wisata</a>
          <a href="#">Sewa Transport</a>
          <a href="#">Open Trip</a>
          <a href="#">Custom Trip</a>
      </div>

      <div class="footer-contact">
          <h4>Kontak</h4>
          <p><i class="fa-solid fa-phone"></i> 0811-2864-286</p>
          <p><i class="fa-solid fa-envelope"></i> info@jawatrip.com</p>
          <p><i class="fa-solid fa-location-dot"></i> Telang Raya, Indonesia</p>
      </div>

      <div class="footer-members">
          <h4>Kelompok 4 :</h4>
          <div class="members-grid">
              <div><i class="fa-solid fa-id-badge"></i> 24-035 | Dani</div>
              <div><i class="fa-solid fa-id-badge"></i> 24-084 | Arif</div>
              <div><i class="fa-solid fa-id-badge"></i> 24-156 | Alif</div>
              <div><i class="fa-solid fa-id-badge"></i> 24-136 | Riko</div>
              <div><i class="fa-solid fa-id-badge"></i> 24-124 | Elsa</div>
              <div><i class="fa-solid fa-id-badge"></i> 24-072 | Safa</div>
          </div>
      </div>
  </div>

  <div class="footer-bottom">
      <p>© 2025 JawaTrip. All Rights Reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
var lat = -8.2325;
var lon = 112.9136;

var map = L.map('map').setView([lat, lon], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
}).addTo(map);

var icon = L.icon({
  iconUrl: '../img/tumpaksewu2.webp',
  iconSize: [40, 40],
  iconAnchor: [20, 40],
  popupAnchor: [0, -40]
});

L.marker([lat, lon], {icon: icon}).addTo(map)
  .bindPopup('<strong>Tumpak Sewu</strong><br>Pronojiwo, Lumajang.')
  .openPopup();
</script>

</body>
</html>
