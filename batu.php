<?php 
include 'koneksi.php'; // Panggil koneksi database
include 'includes/header.php'; 
include 'includes/navbar.php'; 
include 'includes/dashboard_home.php'; 

// Ambil parameter kota dari URL, default ke 'Batu' jika kosong
$kota_terpilih = isset($_GET['kota']) ? $_GET['kota'] : 'Batu';

// --- QUERY DATABASE UNTUK PAKET WISATA ---
// Kita ambil data paket, JOIN dengan tabel kota agar sesuai dengan nama kota di URL
$query_paket = "SELECT p.* FROM paket_wisata p
                JOIN kota k ON p.id_kota = k.id_kota
                WHERE k.nama_kota = '$kota_terpilih'";

$result_paket = mysqli_query($conn, $query_paket);

// --- QUERY DATABASE UNTUK DAFTAR TEMPAT (Opsional jika mau dinamis juga) ---
// Sementara kita biarkan $tempat manual dulu sesuai kodemu, 
// atau kamu bisa buat tabel 'tempat_wisata' terpisah nanti.
$tempat = [
    "Batu" => [
        "Batu Kota" => ["Malioboro kw","Alun-alun Batu","Museum Angkut"], // Contoh data
        "Jawa Timur Park" => ["Jatim Park 1", "Jatim Park 2", "Jatim Park 3", "BNS"],
        "Wisata Alam" => ["Coban Rondo", "Paralayang", "Selecta"]
    ],
    // Tambahkan data dummy untuk kota lain agar tidak error jika $_GET berubah
    "Malang" => ["Wisata Kota" => ["Kampung Warna Warni", "Alun-alun Malang"]],
    "Probolinggo" => ["Wisata Alam" => ["Gunung Bromo", "Air Terjun Madakaripura"]],
    "Banyuwangi" => ["Wisata Alam" => ["Kawah Ijen", "Baluran", "Pulau Merah"]]
];

// Mencegah error jika kota tidak ada di array $tempat
$list_tempat = isset($tempat[$kota_terpilih]) ? $tempat[$kota_terpilih] : [];
?>

<div class="container py-5">
    <h2 class="fw-bold text-center mb-4">PAKET WISATA <?= strtoupper($kota_terpilih); ?></h2>
    
    <div class="row g-4">
        <?php 
        // Cek apakah ada data paket di database
        if (mysqli_num_rows($result_paket) > 0): 
            while ($row = mysqli_fetch_assoc($result_paket)): 
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-1 rounded-4 overflow-hidden h-100">
                    <img src="img/<?= $row['gambar_paket'] ?>" class="card-img-top" style="height:300px; object-fit:cover;" alt="<?= $row['nama_paket'] ?>">
                    
                    <div class="card-body text-white d-flex flex-column" style="background: #145C43;">
                        <h5 class="fw-bold"><?= $row['nama_paket'] ?></h5>
                        
                        <p class="mb-0 mt-3" style="font-size: 13px;"><i>Start from</i></p>
                        
                        <h4 class="fw-bold">
                            Rp<?= number_format($row['harga_per_pax'], 0, ',', '.') ?>
                        </h4>
                        
                        <div class="mt-auto">
                            <a href="wisatamalang.php?id=<?= $row['id_paket'] ?>" class="btn w-100 fw-bold" style="background: #CDAA7D; color: #145C43;">
                                Lihat Paket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">Belum ada paket wisata untuk kota ini.</div>
            </div>
        <?php endif; ?>
    </div>

    <hr class="my-5">

    <h2 class="fw-bold text-center mb-4">Daftar Tempat Wisata di <?= $kota_terpilih; ?></h2>
    <div class="row g-4 justify-content-center">
        <?php if (!empty($list_tempat)): ?>
            <?php foreach ($list_tempat as $area => $list): ?>
            <div class="col-md-6 col-lg-4">
                <div class="p-4 rounded-4 text-center text-white h-100" style="background: #145C43;">
                    <h4 class="fw-bold mb-3" style="color: #CDAA7D;"><?= $area ?></h4>
                    <ul class="list-unstyled">
                        <?php foreach ($list as $l): ?>
                            <li class="mb-1"><?= $l ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">Data tempat wisata belum tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>