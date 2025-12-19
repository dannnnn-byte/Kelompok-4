<?php 
include 'koneksi.php';
include 'includes/header.php'; 
include 'includes/navbar.php'; 

$id_destinasi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_destinasi == 0) {
    die("<div class='alert alert-danger'>Destinasi tidak ditemukan!</div>");
}

// Query detail destinasi
$query = "SELECT * FROM destinasi_wisata WHERE id_destinasi = '$id_destinasi'";
$result = mysqli_query($conn, $query);
$destinasi = mysqli_fetch_assoc($result);

if (!$destinasi) {
    die("<div class='alert alert-danger'>Destinasi tidak ditemukan!</div>");
}

// Parse fasilitas
$fasilitas_array = explode('|', $destinasi['fasilitas'] ?? '');
$tips_array = explode('|', $destinasi['tips_kunjungan'] ?? '');

// Parse gallery images (support multiple images)
$gallery_images = [];
if (!empty($destinasi['gambar_gallery'])) {
    $gallery_images = json_decode($destinasi['gambar_gallery'], true) ?? [];
}

// Jika tidak ada gallery images, gunakan gambar utama
if (empty($gallery_images)) {
    $gallery_images = [
        $destinasi['gambar'],
        $destinasi['gambar'],
        $destinasi['gambar']
    ];
}
?>

<style>
    /* --- HERO SECTION --- */
    .hero-destinasi {
        background-image: linear-gradient(135deg, rgba(20, 92, 67, 0.85) 0%, rgba(13, 61, 42, 0.85) 100%), url('img/<?= htmlspecialchars($destinasi['gambar']); ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .hero-destinasi::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(205, 170, 125, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(30px); }
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .destinasi-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .destinasi-meta {
        font-size: 1.1rem;
        opacity: 0.95;
        margin-bottom: 30px;
    }
    
    .rating-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    
    /* --- CONTENT SECTION --- */
    .destinasi-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #145C43;
        margin-bottom: 40px;
        position: relative;
        padding-bottom: 20px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #CDAA7D 0%, #145C43 100%);
        border-radius: 2px;
    }
    
    /* --- IMAGE GALLERY --- */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 50px;
    }
    
    .gallery-item {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
    }
    
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(20, 92, 67, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
    
    .gallery-icon {
        color: white;
        font-size: 2rem;
    }
    
    /* --- INFO CARDS --- */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }
    
    .info-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border-top: 4px solid #145C43;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    
    .info-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }
    
    .info-label {
        color: #999;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }
    
    .info-value {
        color: #145C43;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .info-desc {
        color: #666;
        font-size: 0.9rem;
    }
    
    /* --- DESCRIPTION --- */
    .description-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        line-height: 1.8;
        margin-bottom: 40px;
    }
    
    .description-box h4 {
        color: #145C43;
        font-weight: 800;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
    
    .description-box p {
        color: #555;
        font-size: 1.05rem;
        text-align: justify;
    }
    
    /* --- FACILITIES --- */
    .facility-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 20px 0;
    }
    
    .facility-tag {
        background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 3px 10px rgba(20, 92, 67, 0.2);
    }
    
    .facility-tag i {
        font-size: 1rem;
    }
    
    /* --- TIPS SECTION --- */
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    
    .tips-list li {
        padding: 15px 20px;
        margin-bottom: 12px;
        background: linear-gradient(135deg, rgba(20, 92, 67, 0.05) 0%, rgba(205, 170, 125, 0.05) 100%);
        border-left: 4px solid #CDAA7D;
        border-radius: 8px;
        color: #555;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .tips-list li::before {
        content: '✓';
        color: #CDAA7D;
        font-weight: 900;
        font-size: 1.3rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    /* --- CTA BUTTON --- */
    .cta-section {
        background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
        padding: 60px;
        border-radius: 15px;
        text-align: center;
        color: white;
        margin-bottom: 50px;
    }
    
    .cta-section h3 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 30px;
    }
    
    .btn-visit {
        background: linear-gradient(135deg, #CDAA7D 0%, #f4c98a 100%);
        color: #145C43;
        padding: 15px 50px;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        display: inline-block;
        text-decoration: none;
    }
    
    .btn-visit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }
    
    /* --- BACK BUTTON --- */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        background: #145C43;
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 30px;
        text-decoration: none;
    }
    
    .btn-back:hover {
        background: #0d3d2a;
        transform: translateX(-5px);
    }
</style>

<!-- Hero Section -->
<div class="hero-destinasi">
    <div class="container">
        <div class="hero-content">
            <h1 class="destinasi-title"><?= htmlspecialchars($destinasi['nama_destinasi']); ?></h1>
            <div class="destinasi-meta">
                <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($destinasi['lokasi']); ?>
            </div>
            <div class="rating-badge">
                <i class="bi bi-star-fill"></i> <?= $destinasi['rating']; ?>/5.0 Rating
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="container mt-5 mb-5">
    <a href="javascript:history.back()" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Main Content -->
<div class="destinasi-section">
    <div class="container">
        
        <!-- Gallery -->
        <h2 class="section-title">Galeri Destinasi</h2>
        <div class="gallery-grid">
            <?php 
            $positions = ['center', 'center right', 'left'];
            for ($i = 0; $i < min(3, count($gallery_images)); $i++): 
            ?>
            <div class="gallery-item">
                <img src="img/<?= htmlspecialchars($gallery_images[$i]); ?>" 
                     alt="<?= htmlspecialchars($destinasi['nama_destinasi']); ?> - Gallery <?= $i + 1; ?>"
                     style="object-position: <?= $positions[$i]; ?>;">
                <div class="gallery-overlay">
                    <i class="bi bi-zoom-in gallery-icon"></i>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        
        <!-- Info Cards -->
        <h2 class="section-title">Informasi Praktis</h2>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="info-label">Jam Operasional</div>
                <div class="info-value"><?= htmlspecialchars($destinasi['jam_buka']); ?> - <?= htmlspecialchars($destinasi['jam_tutup']); ?></div>
                <div class="info-desc">Buka setiap hari</div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div class="info-label">Harga Tiket</div>
                <div class="info-value"><?= htmlspecialchars($destinasi['harga_tiket']); ?></div>
                <div class="info-desc">Per orang</div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-telephone"></i>
                </div>
                <div class="info-label">Hubungi Kami</div>
                <div class="info-value"><?= htmlspecialchars($destinasi['kontak']); ?></div>
                <div class="info-desc">Customer Service</div>
            </div>
            
            <?php if (!empty($destinasi['website'])): ?>
            <div class="info-card">
                <div class="info-icon">
                    <i class="bi bi-globe"></i>
                </div>
                <div class="info-label">Website</div>
                <div class="info-value" style="font-size: 1rem;">
                    <a href="<?= htmlspecialchars($destinasi['website']); ?>" target="_blank" style="color: #145C43; text-decoration: none;">
                        Kunjungi Website
                    </a>
                </div>
                <div class="info-desc">Informasi lengkap</div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Description -->
        <h2 class="section-title">Tentang Destinasi Ini</h2>
        <div class="description-box">
            <h4>Deskripsi Lengkap</h4>
            <p><?= nl2br(htmlspecialchars($destinasi['deskripsi_lengkap'])); ?></p>
        </div>
        
        <!-- Facilities -->
        <h2 class="section-title">Fasilitas & Layanan</h2>
        <div class="description-box">
            <h4>Apa yang Kami Sediakan</h4>
            <div class="facility-tags">
                <?php foreach($fasilitas_array as $f): ?>
                    <?php if (trim($f)): ?>
                    <span class="facility-tag">
                        <i class="bi bi-check-circle"></i>
                        <?= htmlspecialchars(trim($f)); ?>
                    </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Tips -->
        <h2 class="section-title">Tips Kunjungan</h2>
        <div class="description-box">
            <h4>Saran Kami Untuk Anda</h4>
            <ul class="tips-list">
                <?php foreach($tips_array as $tip): ?>
                    <?php if (trim($tip)): ?>
                    <li><?= htmlspecialchars(trim($tip)); ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        
    </div>
</div>

<!-- CTA Section -->
<div class="container mb-5">
    <div class="cta-section">
        <h3>Siap untuk Mengunjungi?</h3>
        <p style="font-size: 1.1rem; margin-bottom: 30px; opacity: 0.95;">
            Pesan paket wisata Anda sekarang dan dapatkan pengalaman tak terlupakan
        </p>
        <a href="javascript:history.back()" class="btn-visit">
            <i class="bi bi-calendar-event"></i> Kembali ke Paket Wisata
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
