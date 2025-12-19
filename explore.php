<?php
session_start();
require_once 'koneksi.php';

include 'includes/header.php';
include 'includes/dashboard_home.php';
?>

<style>
.filter-hero {
    background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
    padding: 80px 0 120px 0;
    color: white;
    position: relative;
}
.filter-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    margin-top: -80px;
    position: relative;
    z-index: 10;
}
.filter-section {
    margin-bottom: 25px;
}
.filter-label {
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    font-size: 0.95rem;
}
.filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.filter-chip {
    background: #f0f0f0;
    border: 2px solid transparent;
    padding: 8px 20px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.9rem;
    font-weight: 600;
}
.filter-chip:hover {
    background: #e0e0e0;
}
.filter-chip.active {
    background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
    color: white;
    border-color: transparent;
}
.price-range-slider {
    width: 100%;
}
.search-box {
    position: relative;
}
.search-box input {
    width: 100%;
    padding: 15px 50px 15px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s;
}
.search-box input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.search-box button {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #145C43 0%, #0d3d2a 100%);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
}
.search-box button:hover {
    transform: translateY(-50%) scale(1.1);
}
.paket-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    height: 100%;
}
.paket-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.paket-img {
    height: 200px;
    object-fit: cover;
    width: 100%;
}
.paket-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(255,255,255,0.95);
    padding: 5px 15px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #667eea;
}
.paket-price {
    font-size: 1.5rem;
    font-weight: 800;
    color: #145C43;
}
.filter-results-header {
    background: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.sort-dropdown {
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    padding: 8px 20px;
    font-size: 0.9rem;
    cursor: pointer;
}
.clear-filters-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
}
.clear-filters-btn:hover {
    background: #c82333;
    transform: scale(1.05);
}
</style>

<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<section class="filter-hero">
    <div class="container text-center">
        <h1 class="fw-bold mb-3">🔍 Cari Paket Wisata Impianmu</h1>
        <p class="lead mb-0">Filter dan temukan destinasi terbaik sesuai kebutuhanmu</p>
    </div>
</section>

<div class="container">
    <div class="filter-card">
        <div class="row">
            <!-- Search Box -->
            <div class="col-12 mb-4">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari destinasi, kota, atau aktivitas...">
                    <button onclick="applyFilters()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Location Filter -->
            <div class="col-md-6 filter-section">
                <div class="filter-label"><i class="bi bi-geo-alt"></i> Lokasi</div>
                <div class="filter-chips" id="locationFilter">
                    <div class="filter-chip" data-value="all" onclick="selectFilter('location', 'all')">Semua</div>
                    <div class="filter-chip" data-value="Batu" onclick="selectFilter('location', 'Batu')">Batu</div>
                    <div class="filter-chip" data-value="Malang" onclick="selectFilter('location', 'Malang')">Malang</div>
                    <div class="filter-chip" data-value="Banyuwangi" onclick="selectFilter('location', 'Banyuwangi')">Banyuwangi</div>
                    <div class="filter-chip" data-value="Bromo" onclick="selectFilter('location', 'Bromo')">Bromo</div>
                </div>
            </div>

            <!-- Duration Filter -->
            <div class="col-md-6 filter-section">
                <div class="filter-label"><i class="bi bi-clock"></i> Durasi</div>
                <div class="filter-chips" id="durationFilter">
                    <div class="filter-chip" data-value="all" onclick="selectFilter('duration', 'all')">Semua</div>
                    <div class="filter-chip" data-value="1" onclick="selectFilter('duration', '1')">1 Hari</div>
                    <div class="filter-chip" data-value="2" onclick="selectFilter('duration', '2')">2 Hari</div>
                    <div class="filter-chip" data-value="3" onclick="selectFilter('duration', '3')">3+ Hari</div>
                </div>
            </div>

            <!-- Price Filter -->
            <div class="col-md-6 filter-section">
                <div class="filter-label"><i class="bi bi-cash"></i> Harga</div>
                <div class="filter-chips" id="priceFilter">
                    <div class="filter-chip" data-value="all" onclick="selectFilter('price', 'all')">Semua</div>
                    <div class="filter-chip" data-value="1" onclick="selectFilter('price', '1')">&lt; 500K</div>
                    <div class="filter-chip" data-value="2" onclick="selectFilter('price', '2')">500K - 1jt</div>
                    <div class="filter-chip" data-value="3" onclick="selectFilter('price', '3')">&gt; 1jt</div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-md-6 filter-section">
                <div class="filter-label"><i class="bi bi-tags"></i> Kategori</div>
                <div class="filter-chips" id="categoryFilter">
                    <div class="filter-chip" data-value="all" onclick="selectFilter('category', 'all')">Semua</div>
                    <div class="filter-chip" data-value="gunung" onclick="selectFilter('category', 'gunung')">Gunung</div>
                    <div class="filter-chip" data-value="pantai" onclick="selectFilter('category', 'pantai')">Pantai</div>
                    <div class="filter-chip" data-value="wisata" onclick="selectFilter('category', 'wisata')">Wisata Kota</div>
                </div>
            </div>

            <!-- Apply Button -->
            <div class="col-12 text-center mt-3">
                <button class="btn btn-lg btn-primary px-5" onclick="applyFilters()" style="border-radius: 50px;">
                    <i class="bi bi-funnel"></i> Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="filter-results-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1 fw-bold">Hasil Pencarian</h5>
            <p class="mb-0 text-muted"><span id="resultCount">0</span> paket ditemukan</p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <select class="sort-dropdown" id="sortBy" onchange="applyFilters()">
                <option value="default">Urutkan: Default</option>
                <option value="price_low">Harga: Terendah</option>
                <option value="price_high">Harga: Tertinggi</option>
                <option value="name">Nama: A-Z</option>
            </select>
            <button class="clear-filters-btn" onclick="clearFilters()">
                <i class="bi bi-x-circle"></i> Reset
            </button>
        </div>
    </div>

    <div id="resultsContainer" class="row g-4 mb-5">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Memuat paket wisata...</p>
        </div>
    </div>
</div>

</div>

<script>
let filters = {
    search: '',
    location: 'all',
    duration: 'all',
    price: 'all',
    category: 'all',
    sort: 'default'
};

function selectFilter(type, value) {
    filters[type] = value;
    
    // Update UI
    const chips = document.querySelectorAll(`#${type}Filter .filter-chip`);
    chips.forEach(chip => {
        chip.classList.toggle('active', chip.dataset.value === value);
    });
}

function clearFilters() {
    filters = {
        search: '',
        location: 'all',
        duration: 'all',
        price: 'all',
        category: 'all',
        sort: 'default'
    };
    
    document.getElementById('searchInput').value = '';
    document.getElementById('sortBy').value = 'default';
    
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.classList.toggle('active', chip.dataset.value === 'all');
    });
    
    applyFilters();
}

function applyFilters() {
    filters.search = document.getElementById('searchInput').value;
    filters.sort = document.getElementById('sortBy').value;
    
    const container = document.getElementById('resultsContainer');
    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
    
    fetch('filter_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(filters)
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('resultCount').textContent = data.results.length;
        
        if (data.results.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Tidak ada paket ditemukan</h4>
                    <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
                </div>
            `;
        } else {
            container.innerHTML = data.results.map(paket => `
                <div class="col-md-6 col-lg-4">
                    <div class="paket-card position-relative">
                        <span class="paket-badge">${paket.durasi}</span>
                        <img src="img/${paket.gambar_paket}" class="paket-img" alt="${paket.nama_paket}">
                        <div class="p-3">
                            <h5 class="fw-bold mb-2">${paket.nama_paket}</h5>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt"></i> ${paket.nama_kota}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Mulai dari</small>
                                    <span class="paket-price">Rp${parseInt(paket.harga_per_pax).toLocaleString('id-ID')}</span>
                                </div>
                                <a href="wisatamalang.php?id=${paket.id_paket}" class="btn btn-primary btn-sm">
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

// Initial load with all filters
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.filter-chip[data-value="all"]').forEach(chip => {
        chip.classList.add('active');
    });
    applyFilters();
});
</script>

<?php include 'includes/footer.php'; ?>
