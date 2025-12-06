<?php 
include 'koneksi.php'; // Pastikan file koneksi database ada
include 'includes/header.php'; 
include 'includes/navbar.php'; 
include 'includes/dashboard_home.php'; 

// 1. AMBIL ID DARI URL (Contoh: detail.php?id=1)
$id_paket = isset($_GET['id']) ? $_GET['id'] : 1; // Default id 1 kalo ga ada

// 2. QUERY DATABASE
// Ambil Detail Paket & Kota
$query_info = "SELECT p.*, k.nama_kota 
               FROM paket_wisata p 
               JOIN kota k ON p.id_kota = k.id_kota 
               WHERE p.id_paket = '$id_paket'";
$result_info = mysqli_query($conn, $query_info);
$data_paket  = mysqli_fetch_assoc($result_info);

// Ambil Itinerary
$query_rundown = "SELECT * FROM paket_itinerary WHERE id_paket = '$id_paket' ORDER BY jam ASC";
$result_rundown = mysqli_query($conn, $query_rundown);

// Ambil Fasilitas
$query_fasilitas = "SELECT * FROM paket_fasilitas WHERE id_paket = '$id_paket'";
$result_fasilitas = mysqli_query($conn, $query_fasilitas);

// Pisahkan fasilitas include dan exclude
$includes = [];
$excludes = [];
while($row = mysqli_fetch_assoc($result_fasilitas)) {
    if($row['jenis'] == 'include') $includes[] = $row['item'];
    else $excludes[] = $row['item'];
}

// Data Dummy Gambar Destinasi (Karena tabel destinasi belum ada, pakai array dulu sesuai kodemu)
$destinasi_list = [
    ["nama" => "Spot Utama", "img" => "img/" . $data_paket['gambar_paket']],
    ["nama" => "Sunrise Point", "img" => "img/bromo.jpg"], // Ganti dengan gambar lain yang ada
];

?>

<style>
    /* --- HERO & BOOKING CARD --- */
    .hero-section {
        background-image: url('img/<?= $data_paket['gambar_paket']; ?>');
        background-size: cover;
        background-position: center;
        padding: 100px 0 150px 0; /* Padding bawah besar untuk space menu pill */
        position: relative;
    }
    .hero-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
    }
    
    /* Booking Card Floating di Kanan */
    .booking-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
        z-index: 10;
    }
    .price-tag { color: #555; font-size: 0.9rem; }
    .price-amount { color: #145C43; font-weight: 800; font-size: 1.8rem; }
    
    /* --- MENU TABS --- */
    .menu-pill-container {
        margin-top: -40px;
        margin-bottom: 40px;
        position: relative;
        z-index: 20;
    }
    .btn-pill {
        border-radius: 50px; padding: 12px 30px; font-weight: 700; border: none;
        background: white; color: #555; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin: 0 5px; transition: 0.3s;
    }
    .btn-pill:hover, .btn-pill.active {
        background: #145C43; color: white; transform: translateY(-3px);
    }

    /* --- TAB CONTENT: RUNDOWN (Timeline Style) --- */
    .timeline { border-left: 2px solid #e0e0e0; margin-left: 10px; padding-left: 20px; }
    .timeline-item { position: relative; margin-bottom: 25px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -26px; top: 5px;
        width: 12px; height: 12px; background: #145C43; border-radius: 50%;
        border: 2px solid white; box-shadow: 0 0 0 2px #145C43;
    }
    .time { font-weight: 800; color: #145C43; min-width: 60px; display: inline-block; }
    .activity { color: #555; }

    /* --- TAB CONTENT: FACILITIES --- */
    .facility-box {
        background: #f8f9fa; padding: 20px; border-radius: 10px; height: 100%;
    }
    .facility-list { list-style: none; padding: 0; }
    .facility-list li { margin-bottom: 12px; display: flex; align-items: center; }
    .icon-include { color: #28a745; margin-right: 10px; font-size: 1.2rem; }
    .icon-exclude { color: #dc3545; margin-right: 10px; font-size: 1.2rem; }
    
    /* Utility */
    .tab-content-area { display: none; animation: fadeIn 0.5s; }
    .tab-content-area.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container position-relative text-white">
        <div class="row align-items-center">
            
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark mb-2">OPEN TRIP</span>
                <h1 class="fw-bold display-4"><?= $data_paket['nama_paket']; ?></h1>
                <p class="lead"><i class="bi bi-geo-alt"></i> <?= $data_paket['nama_kota']; ?> | <i class="bi bi-clock"></i> <?= $data_paket['durasi']; ?></p>
            </div>

            <div class="col-lg-5">
                <div class="booking-card text-dark">
                    <p class="price-tag mb-0">Start From</p>
                    <div class="price-amount mb-3">Rp<?= number_format($data_paket['harga_per_pax'], 0, ',', '.'); ?></div>
                    
                    <form action="pesan.php" method="GET">
                        <input type="hidden" name="id_paket" value="<?= $id_paket ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Tanggal</label>
                            <input type="date" name="tgl" class="form-control">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label fw-bold">Dewasa</label>
                                <input type="number" name="jml" value="1" min="1" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background-color: #0d6efd;">Book Now</button>
                    </form>
                    
                    <hr>
                    <div class="d-flex align-items-center gap-3">
                         <i class="bi bi-headset fs-2 text-primary"></i>
                         <div>
                             <small class="text-muted">Butuh Bantuan?</small><br>
                             <strong>0812-3456-7890</strong>
                         </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container">
    <div class="menu-pill-container d-flex justify-content-center">
        <button onclick="openTab('destination')" class="btn-pill active" id="btn-destination">
            <i class="bi bi-geo-alt-fill"></i> Destination
        </button>
        <button onclick="openTab('rundown')" class="btn-pill" id="btn-rundown">
            <i class="bi bi-calendar-check"></i> Itinerary
        </button>
        <button onclick="openTab('facilities')" class="btn-pill" id="btn-facilities">
            <i class="bi bi-check-circle"></i> Facilities
        </button>
    </div>
</div>

<div class="container mb-5">
    
    <div id="content-destination" class="tab-content-area active">
        <h3 class="fw-bold mb-4">Galeri Destinasi</h3>
        <div class="row g-4">
            <?php foreach($destinasi_list as $item): ?>
            <div class="col-md-6">
                <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100">
                    <img src="<?= $item['img']; ?>" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                    <div class="card-body bg-light">
                        <h5 class="fw-bold m-0"><?= $item['nama']; ?></h5>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-5">
            <h4 class="fw-bold">Description</h4>
            <p class="text-muted"><?= $data_paket['deskripsi_wisata']; ?></p>
        </div>
    </div>

    <div id="content-rundown" class="tab-content-area">
        <h3 class="fw-bold mb-4">Travel Itinerary</h3>
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h5 class="text-primary fw-bold mb-4"><i class="bi bi-circle-fill small"></i> Day 1 - Itinerary Trip</h5>
            
            <div class="timeline">
                <?php while($run = mysqli_fetch_assoc($result_rundown)): ?>
                <div class="timeline-item">
                    <span class="time"><?= $run['jam']; ?></span>
                    <span class="activity"><?= $run['kegiatan']; ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div id="content-facilities" class="tab-content-area">
        <h3 class="fw-bold mb-4">Fasilitas Paket</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="facility-box">
                    <h5 class="fw-bold mb-3">Included</h5>
                    <ul class="facility-list">
                        <?php foreach($includes as $inc): ?>
                        <li><i class="bi bi-tag-fill icon-include"></i> <?= $inc; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mt-4 mt-md-0">
                <div class="facility-box bg-white border">
                    <h5 class="fw-bold mb-3">Excluded</h5>
                    <ul class="facility-list">
                        <?php foreach($excludes as $exc): ?>
                        <li><i class="bi bi-x-circle-fill icon-exclude"></i> <?= $exc; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function openTab(tabName) {
    // 1. Sembunyikan semua konten
    var contents = document.getElementsByClassName("tab-content-area");
    for (var i = 0; i < contents.length; i++) {
        contents[i].classList.remove("active");
    }

    // 2. Hapus class 'active' dari semua tombol
    var buttons = document.getElementsByClassName("btn-pill");
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove("active");
    }

    // 3. Tampilkan konten yang dipilih & aktifkan tombolnya
    document.getElementById("content-" + tabName).classList.add("active");
    document.getElementById("btn-" + tabName).classList.add("active");
}
</script>

<?php include 'includes/footer.php'; ?>