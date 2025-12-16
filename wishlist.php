<?php
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/dashboard_home.php';

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header("Location: login.php");
    exit;
}
?>

<style>
.wishlist-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    color: white;
}
.wishlist-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s;
    position: relative;
}
.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.wishlist-img {
    height: 200px;
    object-fit: cover;
    width: 100%;
}
.remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: #dc3545;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10;
}
.remove-btn:hover {
    background: #dc3545;
    color: white;
    transform: rotate(90deg);
}
.empty-state {
    text-align: center;
    padding: 100px 20px;
}
.empty-state i {
    font-size: 5rem;
    color: #ddd;
    margin-bottom: 20px;
}
.price-tag {
    background: #145C43;
    color: white;
    padding: 5px 15px;
    border-radius: 50px;
    font-weight: 600;
}
</style>

<section class="wishlist-hero">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-heart-fill"></i> Destinasi Favorit Saya</h1>
        <p class="mb-0">Kumpulan destinasi wisata impianmu</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Total: <span id="wishlistCount" class="text-primary">0</span> Destinasi</h4>
            <a href="wisata.php" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Jelajahi Lebih Banyak
            </a>
        </div>

        <div id="wishlistContainer" class="row g-4">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>
</section>

<script>
function loadWishlist() {
    fetch('wishlist_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_wishlist'
    })
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('wishlistContainer');
        document.getElementById('wishlistCount').textContent = data.wishlist.length;
        
        if (data.wishlist.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-heart"></i>
                        <h4>Belum Ada Favorit</h4>
                        <p class="text-muted">Mulai tambahkan destinasi favoritmu sekarang!</p>
                        <a href="wisata.php" class="btn btn-primary mt-3">
                            <i class="bi bi-compass"></i> Jelajahi Destinasi
                        </a>
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = data.wishlist.map(item => `
                <div class="col-md-6 col-lg-4" data-wishlist-id="${item.id}">
                    <div class="wishlist-card">
                        <button class="remove-btn" onclick="removeWishlist(${item.id}, ${item.id_paket})">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <img src="img/${item.gambar_paket}" class="wishlist-img" alt="${item.nama_paket}">
                        <div class="p-3">
                            <h5 class="fw-bold mb-2">${item.nama_paket}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt"></i> ${item.nama_kota} 
                                <span class="ms-2"><i class="bi bi-clock"></i> ${item.durasi}</span>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">Rp ${parseInt(item.harga_per_pax).toLocaleString('id-ID')}</span>
                                <a href="wisatamalang.php?id=${item.id_paket}" class="btn btn-sm btn-outline-primary">
                                    Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    });
}

function removeWishlist(wishlistId, idPaket) {
    if (!confirm('Hapus dari favorit?')) return;
    
    fetch('wishlist_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle_wishlist&id_paket=${idPaket}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-wishlist-id="${wishlistId}"]`).remove();
            loadWishlist();
        }
    });
}

loadWishlist();
</script>

<?php include 'includes/footer.php'; ?>
