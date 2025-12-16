<?php
session_start();


include 'koneksi.php';

$role = $_SESSION['role'] ?? 'guest';
?>

<?php if (isset($_SESSION['login_success'])): ?>
<div id="loginToast" class="login-toast show">
    <div class="toast-icon">✔</div>
    <div class="toast-text"><?= $_SESSION['login_success']; ?></div>
</div>
<?php unset($_SESSION['login_success']); ?>
<?php endif; ?>




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

 <?php
$q = mysqli_query($conn,
  "SELECT * FROM destinasi_populer WHERE aktif=1 ORDER BY id DESC");
?>

<div class="destination-wrapper">

<?php while ($d = mysqli_fetch_assoc($q)) : ?>

  <div class="dest-card-wrapper">

    <!-- CARD DESTINASI -->
    <a href="destinasi/<?= $d['slug'] ?>.php" class="promo-link">
      <div class="dest-card">
        <img src="uploads/destinasi/<?= $d['gambar'] ?>" alt="<?= $d['nama'] ?>">
        <div class="dest-overlay">
          <p class="dest-category">WISATA</p>
          <h3 class="dest-title"><?= strtoupper($d['nama']) ?></h3>
          <span class="dest-btn">LIHAT SELENGKAPNYA</span>
        </div>
      </div>
    </a>

    <!-- TOMBOL HAPUS (ADMIN ONLY) -->
    <?php if (($_SESSION['role'] ?? '') === 'admin') : ?>
      <form action="admin/hapus_destinasi_populer.php"
            method="POST"
            class="delete-dest-form"
            onsubmit="return confirm('Yakin ingin menghapus destinasi ini?');">
        <input type="hidden" name="id" value="<?= $d['id'] ?>">
        <button type="submit" class="btn-delete" title="Hapus Destinasi">
          Hapus
        </button>
      </form>
    <?php endif; ?>

  </div>

<?php endwhile; ?>

<?php if (($_SESSION['role'] ?? '') === 'admin') : ?>
  <!-- CARD TAMBAH DESTINASI -->
  <a href="admin/tambah_destinasi_populer.php" class="promo-link">
    <div class="dest-card add-dest">
      <div class="add-icon">+</div>
      <p>Tambah Destinasi</p>
    </div>
  </a>
<?php endif; ?>

</div>



    </div>
  </div>
</section>

          
          

        </div>
      </div>
    </section>

   <style>
.promo-section { padding: 50px 0; }
.promo-title { font-size: 32px; font-weight: 800; color: #28a745; }
.login-toast {
    position: fixed;
    top: -100px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    opacity: 0;
    z-index: 99999;
    transition: all 0.6s ease;
}

.login-toast.show {
    top: 30px;
    opacity: 1;
}

.login-toast.hide {
    opacity: 0;
    top: -80px;
}

.toast-icon {
    font-size: 22px;
}

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

.add-destination-card{
  border:3px dashed #28a745;
  background:rgba(255,255,255,0.5);
  display:flex;
  align-items:center;
  justify-content:center;
  min-height:260px;
  transition:.3s;
}
.add-destination-card:hover{
  background:rgba(40,167,69,.15);
  transform:scale(1.03);
}
.add-destination-content{
  text-align:center;
  color:#28a745;
  font-weight:800;
}
.add-icon{
  font-size:64px;
  line-height:1;
}
.login-toast{
  position:fixed;
  top:30px;
  left:50%;
  transform:translateX(-50%);
  background:#28a745;
  color:#fff;
  padding:15px 25px;
  border-radius:10px;
  z-index:9999;
}

.add-dest {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  border: 2px dashed #28a745;
  background: #f8fff8;
  cursor: pointer;
  transition: .3s;
}

.add-dest:hover {
  background: #eaffea;
}

.add-icon {
  font-size: 60px;
  color: #28a745;
  font-weight: bold;
}


.dest-card-wrapper {
  position: relative;
}

.delete-dest-form {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 10;
}

.btn-delete {
  background: rgba(0,0,0,0.65);
  border: none;
  color: #fff;
  font-size: 18px;
  padding: 6px 10px;
  border-radius: 50%;
  cursor: pointer;
}

.btn-delete:hover {
  background: #dc3545;
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
<script>
    let toast = document.getElementById("loginToast");

    if (toast) {
        // hilang otomatis setelah 3 detik
        setTimeout(() => {
            toast.classList.remove("show");
            toast.classList.add("hide");

            // hapus setelah animasi selesai
            setTimeout(() => toast.remove(), 1000);
        }, 3000);
    }
    
</script>



</body>
</html> 