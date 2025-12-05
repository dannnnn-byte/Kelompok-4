<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/dashboard_home.php'; ?>


<?php  
$kota = $_GET['kota'] ?? '';

// DATA PAKET WISATA
$paket = [
    "Batu" => [
        ["Batu 2 Hari 1 Malam", "Rp300.000", "img/jtp1.png"],
        ["Batu 3 Hari 2 Malam", "Rp530.000", "img/bsz.jpg"],
        ["Batu City Tour", "Rp250.000", "img/pelangi.webp"],
        ["Wisata Alam Batu", "Rp400.000", "img/alambatu1.jpg"]
    ]
];

// DATA TEMPAT WISATA
$tempat = [
    "Batu" => [
        "Batu Kota" => [
        "Malioboro","Keraton Jogja","Tamansari","Alun-alun Kidul","Taman Pintar",
        "Tugu Jogja","Gembira Loka","Taman Pelangi"
    ],
    "Jawa Timur Park" => [
            "HeHa Forest","Jeep Merapi","Ullen Sentalu","Landmark Merapi",
            "The Lost World Castle","Museum Merapi","Candi Mendut",
            "Stonehenge Merapi","Agrowisata Bhumi Merapi"
        ],
    "Wisata Alam Batu" => [
            "Pantai Parangtritis","Gumuk Pasir","Kedung Pengilon","Hutan Pinus Mangunan"
        ],
    "Pantai" => [
            "Mahaloka Paradise","Pronosutan View","Kedung Pedhut"
        ]
    ]
];
?>

<div class="container py-5">
    <h2 class="fw-bold text-center mb-4">PAKET WISATA <?= strtoupper($kota); ?></h2>
    <div class="row g-4">
        <?php foreach ($paket[$kota] as $p): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-1 rounded-4 overflow-hidden">
                    <img src="<?= $p[2] ?>" class="card-img-top" style="height:300px; object-fit:cover;">
                    <div class="card-body text-white" style="background: #145C43;">
                        <h5 class="fw-bold"><?= $p[0] ?></h5>
                        <p class="mb-0 mt-3" style="font-size: 13px;"><i>Start from</i></p>
                        <p class="fw-bold"><?= $p[1] ?></p>
                        <a href="wisatamalang.php" class="btn w-100" style="background: #CDAA7D;">Lihat Paket</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr class="my-5">

    <h2 class="fw-bold text-center mb-4">Daftar Tempat Wisata di <?= $kota; ?></h2>
    <div class="row g-4">
        <?php foreach ($tempat[$kota] as $area => $list): ?>
        <div class="col-md-6 col-lg-6">
            <div class="p-4 rounded-4 text-center text-white" style="background: #145C43;">
                <h4 class="fw-bold mb-3"><?= $area ?></h4>
                <?php foreach ($list as $l): ?>
                    <p class="m-0"><?= $l ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>