<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ========================
   KONEKSI DATABASE
======================== */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "jawatrip";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

/* ========================
   TEMPAT / DESTINASI
======================== */
$tempat = "Wisata Bahari Lamongan";

/* ========================
   SIMPAN REVIEW (POST)
======================== */
if (isset($_POST["submit_review"])) {

    $nama     = $_POST["nama"];
    $rating   = $_POST["rating"];
    $komentar = $_POST["komentar"];

    $stmt = $conn->prepare("INSERT INTO reviews (tempat, nama, rating, komentar) VALUES (?,?,?,?)");
    $stmt->bind_param("ssis", $tempat, $nama, $rating, $komentar);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Terima kasih! Ulasan Anda berhasil dikirim.');</script>";
}

/* ========================
   AMBIL DATA REVIEW
======================== */
$reviews = [];
$result = $conn->query("SELECT * FROM reviews WHERE tempat='$tempat' ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JawaTrip - Wisata Bahari Lamongan</title>

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

.logo-title { display:flex; align-items:center; gap:12px; margin-bottom:40px; }
.logo-title img { width:45px; height:45px; border-radius:50%; border:2px solid #00b050; }
.logo-title h3 { font-size:22px; color:#00b050; font-weight:800; }

/* Menu items */
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
    background-image: url('../img/wbl4.webp'); /* ganti dengan file background kamu */
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

/* Map */
#map { 
    height:500px; 
    border-radius:12px; 
    box-shadow:0 6px 12px rgba(0,0,0,0.15); 
    margin-top:20px; 
}

.review-section {
    margin-top: 50px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    backdrop-filter: blur(8px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}
.review-title {
    font-size: 28px;
    font-weight: 800;
    color: #ffdd57;
    margin-bottom: 25px;
}
.review-card {
    background: rgba(0, 0, 0, 0.45);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.review-stars {
    color: #ffd700;
    margin-bottom: 10px;
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
  <h1>Wisata Bahari Lamongan (WBL)</h1>
  <p>One-stop Family Waterpark & Coastal Recreation</p>
</header>

<!-- Sejarah -->
<section class="sejarah-bromo mt-4">
  <div class="content row align-items-center">
    <div class="col-md-6">
      <h3>Sejarah & Daya Tarik Wisata Bahari Lamongan</h3>
      <p>
        Wisata Bahari Lamongan (WBL) didirikan sejak 14 November 2004 di kawasan Pantai Tanjung Kodok, Paciran.
        WBL menggabungkan konsep wisata pantai, wahana permainan air, dan zona tematik edukasi budaya sehingga
        menjadi destinasi keluarga populer di Jawa Timur.
      </p>
      <p>
        Di area seluas puluhan hektar ini terdapat banyak wahana: Istana Bawah Laut, Goa Insectarium, Waterboom,
        Jet Coaster, Space Shuttle, Anjungan Wali Songo, serta berbagai permainan anak dan wahana edukasi.
        WBL juga berdekatan dengan Maharani Zoo & Goa sehingga sering menjadi paket wisata terintegrasi.
      </p>
    </div>
    <div class="col-md-6 text-center">
      <img src="../img/wbl2.webp" alt="Wisata Bahari Lamongan">
    </div>
  </div>
</section>

<!-- Map Info -->
<div class="map-info">
    <h4>Wisata Bahari Lamongan (WBL)</h4>
    <p>Paciran, Kabupaten Lamongan</p>
    <p>Ketinggian: Pantai (sekitar permukaan laut)</p>
    <p>Provinsi: Jawa Timur, Indonesia</p>
    <p>
      <a href="https://www.google.com/maps/place/Wisata+Bahari+Lamongan/@-6.86614,112.36035,15z" target="_blank">
        Buka Google Maps
      </a>
    </p>
</div>

<div id="map"></div>

  <!-- ================= FORM REVIEW ================= -->
<h4 class="text-white mt-5">Tambah Ulasan</h4>

<form action="" method="POST" class="p-3 rounded-3" style="background: rgba(0,0,0,0.4);">

    <div class="mb-3">
        <label class="text-white">Nama Anda</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="text-white">Rating</label>
        <select name="rating" class="form-control" required>
            <option value="5">★★★★★ (5)</option>
            <option value="4">★★★★☆ (4)</option>
            <option value="3">★★★☆☆ (3)</option>
            <option value="2">★★☆☆☆ (2)</option>
            <option value="1">★☆☆☆☆ (1)</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="text-white">Komentar</label>
        <textarea name="komentar" class="form-control" rows="3" required></textarea>
    </div>

    <button type="submit" name="submit_review" class="btn btn-warning fw-bold w-100">
        Kirim Ulasan
    </button>
</form>

<!-- ================= TAMPILKAN REVIEW ================= -->
<section class="review-section">
    <h2 class="review-title">Ulasan Pengunjung</h2>

    <?php if (count($reviews) === 0): ?>

        <p class="text-white">Belum ada ulasan. Jadilah yang pertama!</p>

    <?php else: ?>

        <?php foreach ($reviews as $r): ?>
            <div class="review-card">
                <h5><?= htmlspecialchars($r['nama']) ?></h5>

                <div class="review-stars">
                    <?= str_repeat("★", $r['rating']); ?>
                    <?= str_repeat("☆", 5 - $r['rating']); ?>
                </div>

                <p><?= nl2br(htmlspecialchars($r['komentar'])) ?></p>
                <small><?= $r['created_at'] ?></small>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</section>

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
          <p><i class="fa-solid fa-phone"></i> 0322-666111</p>
          <p><i class="fa-solid fa-envelope"></i> info@wisatabaharilamongan.com</p>
          <p><i class="fa-solid fa-location-dot"></i> Paciran, Lamongan</p>
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
/*
  Koordinat WBL (sumber: situs resmi WBL & publikasi)
  lat: -6.86614
  lon: 112.36035
*/
var lat = -6.86614;
var lon = 112.36035;

var map = L.map('map').setView([lat, lon], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
}).addTo(map);

var wblIcon = L.icon({
  iconUrl: '../img/wbl.jpg',
  iconSize: [40, 40],
  iconAnchor: [20, 40],
  popupAnchor: [0, -40]
});

L.marker([lat, lon], {icon: wblIcon}).addTo(map)
  .bindPopup('<strong>Wisata Bahari Lamongan</strong><br>Paciran, Lamongan.')
  .openPopup();
</script>
</body>
</html>
