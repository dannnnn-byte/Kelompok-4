<?php include 'includes/header.php'; ?>
<?php include 'includes/dashboard_home.php'; ?>

<div class="main-content">

    <?php include 'includes/navbar.php'; ?>

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

   <style>
.promo-section { padding: 50px 0; }
.promo-title { font-size: 32px; font-weight: 800; color: #28a745; }

/* Container Scroll */
.promo-container {
    display: flex;
    gap: 25px;
    overflow-x: auto;
    padding-bottom: 20px;
    scroll-snap-type: x mandatory;
}

/* Card */
.promo-card {
    min-width: 320px;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transition: .25s ease;
    scroll-snap-align: start;
}

.promo-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.promo-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.18);
}

/* Image */
.promo-img {
    position: relative;
}

.promo-img img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
}

.promo-tag {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #111;
    color: white;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 6px;
}

/* Info */
.promo-info {
    padding: 18px 20px;
}

.promo-info h5 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 6px;
}

.promo-info p {
    margin: 0;
    font-size: 14px;
    color: #555;
}

.promo-price {
    margin-top: 10px;
    color: #ff4500;
    font-weight: 800;
    font-size: 20px;
}
</style>

   <div class="promo-container">

    <a href="batu.php?kota=Batu" class="promo-link">
        <div class="promo-card">
            <div class="promo-img">
                <span class="promo-tag">PAKET WISATA</span>
                <img src="img/batu.webp" alt="Batu Tour">
            </div>
            <div class="promo-info">
                <h5>BATU - MALANG</h5>
                <p>Open Trip • 9 Mar 2026</p>
                <p class="promo-price">Rp 350.000</p>
            </div>
        </div>
    </a>

    <a href="lumajang.php?kota=Ijen" class="promo-link">
        <div class="promo-card">
            <div class="promo-img">
                <span class="promo-tag">PAKET WISATA</span>
                <img src="img/lumajang.webp" alt="Lumajang">
            </div>
            <div class="promo-info">
                <h5>Lumajang</h5>
                <p>Private Trip • 10 Jan 2026</p>
                <p class="promo-price">Rp 450.000</p>
            </div>
        </div>
    </a>

    <a href="destinasi_detail.php?kota=TumpakSewu" class="promo-link">
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

    <a href="destinasi_detail.php?kota=MuseumAngkut" class="promo-link">
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


    <?php include 'includes/footer.php'; ?>

</div> 

</body>
</html>