<?php 
include 'includes/header.php'; 

// --- DATA DUMMY ---
$kota = isset($_GET['kota']) ? $_GET['kota'] : 'Malang';
$judul_paket = "PAKET WISATA " . strtoupper($kota) . " 2 HARI";
$sub_judul = "2D1N PAKET 1";
$deskripsi = "Paket Wisata $kota 2 Hari 1 Malam yang kami tawarkan untuk mengisi liburan Anda. Nikmati pengalaman tak terlupakan bersama kami.";
$background_hero = "img/jtp3.jpeg"; 

$destinasi_list = [
    ["nama" => "Jatim Park 3 (Legend Star)", "img" => "img/jtp3.jpeg"],
    ["nama" => "Museum Angkut", "img" => "img/musang.jpeg"],
    ["nama" => "Alun-Alun Batu", "img" => "img/batu.webp"],
    ["nama" => "Coban Rondo", "img" => "img/cobanrondo.jpeg"]
];
?>

<style>
    /* 1. HERO SECTION */
    .hero-banner {
        position: relative;
        height: 600px;
        background-image: url('<?= $background_hero; ?>');
        background-size: cover;
        background-position: center;
        /* Overlay gradasi merah/orange seperti contoh */
        display: flex;
        align-items: center;
        margin-top: -80px; 
        padding-top: 80px;
    }
    .hero-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to right, rgba(220, 53, 69, 0.7), rgba(0,0,0,0.1));
        z-index: 1;
    }
    .hero-content { position: relative; z-index: 2; color: white; padding-left: 20px; }
    .hero-title-main { font-weight: 700; letter-spacing: 2px; font-size: 1.2rem; }
    .hero-title-sub { font-weight: 800; font-size: 4rem; line-height: 1; margin: 10px 0; }

    /* 2. MENU PILL (TOMBOL TENGAH) */
    .menu-pill-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: -25px; /* Naik ke atas menumpuk banner */
        position: relative;
        z-index: 10;
        margin-bottom: 50px;
    }
    .btn-pill {
        border-radius: 50px;
        padding: 10px 30px;
        font-weight: 700;
        color: white;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        transition: 0.3s;
        border: none;
    }
    .btn-pill:hover { transform: translateY(-3px); color: white; }
    .bg-teal { background-color: #2b7a78; } /* Warna tombol destination */
    .bg-pink { background-color: #e91e63; } /* Warna tombol lainnya */

    /* 3. CARD DESTINASI (YANG DIPERBAIKI) */
    .destinasi-card {
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        height: 350px; /* Tinggi fix agar kotak terlihat rapi */
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .destinasi-card img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* PENTING: Memotong gambar agar penuh kotak */
        transition: transform 0.5s ease;
    }
    .destinasi-card:hover img { transform: scale(1.1); }
    
    .card-overlay {
        position: absolute;
        bottom: 0; left: 0; width: 100%;
        padding: 25px;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); /* Gradasi hitam di bawah */
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .card-title {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
    }
    .btn-overview {
        background-color: #e91e63;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <div class="hero-title-main"><?= $judul_paket; ?>:</div>
                <div class="hero-title-sub"><?= $sub_judul; ?></div>
                <p class="mb-4"><?= $deskripsi; ?></p>
                <a href="#" class="btn btn-danger rounded-pill px-4 fw-bold">Selengkapnya</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="menu-pill-container flex-wrap">
        <a href="#" class="btn-pill bg-teal"><i class="bi bi-geo-alt-fill"></i> Destination</a>
        <a href="#" class="btn-pill bg-pink"><i class="bi bi-calendar-check"></i> Rundown</a>
        <a href="#" class="btn-pill bg-pink"><i class="bi bi-check-circle"></i> Facilities</a>
        <a href="#" class="btn-pill bg-pink"><i class="bi bi-tag-fill"></i> Pricing</a>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4"> 
        <?php foreach($destinasi_list as $item): ?>
            <div class="col-12 col-md-6"> 
                <div class="destinasi-card">
                    <img src="<?= $item['img']; ?>" alt="<?= $item['nama']; ?>">
                    <div class="card-overlay">
                        <h3 class="card-title"><?= $item['nama']; ?></h3>
                        <span class="btn-overview">Overview ></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>