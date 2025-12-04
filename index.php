<?php include 'includes/header.php'; ?>

<!-- ================= SIDEBAR & HAMBURGER ================= -->
<?php include 'includes/dashboard_home.php'; ?>

<!-- ================= NAVBAR ================= -->
<?php include 'includes/navbar.php'; ?>

<!-- ================= HERO SECTION ================= -->
<section class="hero">
  <div class="hero-overlay">
    <div class="hero-content text-center text-white">
      <h1 class="display-4 fw-bold">Paket Wisata Terbaik <span class="text-warning">JawaTrip</span></h1>
      <p class="lead mt-3">
        Nikmati liburan tak terlupakan di berbagai destinasi wisata terbaik Jawa Timur bersama kami!
        Liburan asik tanpa harus mikirin ini-itu karena travel wisata Jawa Timur JawaTrip.id
        sudah punya paket wisata komplit biar hari liburmu beneran jadi liburan asyik dengan tour planner dan guide terbaik. 
      </p>
      <a href="wisata.php" class="btn btn-success btn-lg mt-3 fw-semibold">Lihat Paket</a>
    </div>
  </div>
</section>

<!-- ================= REKOMENDASI (DIPINDAH KE ATAS PROMO) ================= -->
<link rel="stylesheet" href="assets/wisataPic.css">
<section class="destination-section">
  <div class="container py-5">
    <h2 class="text-center fw-bold text-success mb-5">Rekomendasi Destinasi Populer di Jawa Timur</h2>

    <div class="destination-wrapper">

      <a href="destinasi/bromo.php" class="promo-link">
        <div class="dest-card">
          <img src="img/bromo4.jpg" alt="bromo">
          <div class="dest-overlay">
            <p class="dest-category">WISATA</p>
            <h3 class="dest-title">BROMO - LUMAJANG</h3>
            <span class="dest-btn">LIHAT SELENGKAPNYA</span>
          </div>
        </div>
      </a>

      <a href="destinasi/tumpaksewu.php" class="promo-link">
        <div class="dest-card">
          <img src="img/tumpaksewu.jpg" alt="tumpaksewu">
          <div class="dest-overlay">
            <p class="dest-category">WISATA</p>
            <h3 class="dest-title">TUMPAK SEWU - LUMAJANG</h3>
            <span class="dest-btn">LIHAT SELENGKAPNYA</span>
          </div>
        </div>
      </a>

      <a href="destinasi/kawahijen.php" class="promo-link">
        <div class="dest-card">
          <img src="img/ijen2.jpg" alt="kawahijen">
          <div class="dest-overlay">
            <p class="dest-category">WISATA</p>
            <h3 class="dest-title">KAWAH IJEN - BANYUWANGI</h3>
            <span class="dest-btn">LIHAT SELENGKAPNYA</span>
          </div>
        </div>
      </a>

      <a href="destinasi/museumangkut.php" class="promo-link">
        <div class="dest-card">
          <img src="img/angkut.webp" alt="angkut">
          <div class="dest-overlay">
            <p class="dest-category">WISATA</p>
            <h3 class="dest-title">MUSEUM ANGKUT - MALANG</h3>
            <span class="dest-btn">LIHAT SELENGKAPNYA</span>
          </div>
        </div>
      </a>

      <a href="destinasi/wbl.php" class="promo-link">
        <div class="dest-card">
          <img src="img/wbl.jpg" alt="wbl">
          <div class="dest-overlay">
            <p class="dest-category">WISATA</p>
            <h3 class="dest-title">WISATA BAHARI LAMONGAN - LAMONGAN</h3>
            <span class="dest-btn">LIHAT SELENGKAPNYA</span>
          </div>
        </div>
      </a>

    </div>
  </div>
</section>

<!-- ================= PROMO WISATA (DIPINDAH KE BAWAH REKOMENDASI) ================= -->
<style>
.promo-section { padding: 50px 0; }
.promo-title { font-size: 32px; font-weight: 800; color: #28a745; }
.promo-container { display: flex; overflow-x: auto; gap: 25px; padding-bottom: 15px; }
.promo-card { min-width: 320px; background: white; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.12); transition: .2s; }
.promo-link { text-decoration: none; color: inherit; display: block; }
.promo-card:hover { transform: translateY(-5px); }
.promo-img img { width: 100%; height: 220px; object-fit: cover; }
.promo-tag { position: absolute; background: black; color: white; padding: 6px 14px; font-size: 13px; font-weight: 700; top: 10px; left: 10px; border-radius: 6px; }
.promo-img { position: relative; }
.promo-info { padding: 18px; }
.promo-price { color: #ff4500; font-weight: 800; font-size: 20px; }
</style>

<section class="promo-section">
  <div class="container">

    <h2 class="promo-title">Promo Paket Wisata Domestik Murah! 🌴✈️</h2>

    <div class="promo-container">

      <a href="destinasi/bromo.php" class="promo-link">
        <div class="promo-card">
          <div class="promo-img">
            <span class="promo-tag">PAKET WISATA</span>
            <img src="img/bromo4.jpg" alt="Bromo Tour">
          </div>
          <div class="promo-info">
            <h5>Bromo Sunrise Tour</h5>
            <p>Open Trip • 9 Mar 2026</p>
            <p class="promo-price">Rp 350.000</p>
          </div>
        </div>
      </a>

      <a href="destinasi/kawahijen.php" class="promo-link">
        <div class="promo-card">
          <div class="promo-img">
            <span class="promo-tag">PAKET WISATA</span>
            <img src="img/ijen2.jpg" alt="Kawah Ijen Tour">
          </div>
          <div class="promo-info">
            <h5>Kawah Ijen Blue Fire</h5>
            <p>Private Trip • 10 Jan 2026</p>
            <p class="promo-price">Rp 450.000</p>
          </div>
        </div>
      </a>

      <a href="destinasi/tumpaksewu.php" class="promo-link">
        <div class="promo-card">
          <div class="promo-img">
            <span class="promo-tag">PAKET WISATA</span>
            <img src="img/tumpaksewu.jpg" alt="Tumpak Sewu">
          </div>
          <div class="promo-info">
            <h5>Tumpak Sewu Waterfall</h5>
            <p>Open Trip • 5 Feb 2026</p>
            <p class="promo-price">Rp 300.000</p>
          </div>
        </div>
      </a>

      <a href="destinasi/museumangkut.php" class="promo-link">
        <div class="promo-card">
          <div class="promo-img">
            <span class="promo-tag">PAKET WISATA</span>
            <img src="img/angkut.webp" alt="Museum Angkut">
          </div>
          <div class="promo-info">
            <h5>Museum Angkut Malang</h5>
            <p>Wisata Keluarga</p>
            <p class="promo-price">Rp 120.000</p>
          </div>
        </div>
      </a>

    </div>
  </div>
</section>

<!-- ================= ALASAN MEMILIH JAWATRIP ================= -->
<section class="why-jawatrip py-5">
  <div class="container">
    <h2 class="text-center fw-bold text-success mb-5">Kenapa Harus <span class="text-warning">JawaTrip</span>?</h2>
    <div class="row g-4">

      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <img src="img/paketbromo.jpg" class="card-img-top" alt="Paket Wisata">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Paket Wisata Lengkap</h5>
            <p>Transportasi, akomodasi, hingga tour guide profesional.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <img src="img/tourguide.jpg" class="card-img-top" alt="Tour Guide">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Tour Guide Berpengalaman</h5>
            <p>Guide ramah dan berpengalaman menemani perjalanan Anda.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <img src="img/liburan.jpg" class="card-img-top" alt="Liburan Tanpa Ribet">
          <div class="card-body">
            <h5 class="card-title fw-semibold">Liburan Tanpa Ribet</h5>
            <p>Cukup nikmati perjalanan, semua itinerary kami siapkan.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>

</body>
</html>
