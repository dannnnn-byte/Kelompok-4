<?php include 'includes/header.php'; ?>
<?php include 'includes/dashboard_home.php'; ?>

<div class="main-content">
  <?php include 'includes/navbar.php'; ?>
  <div class="container py-5">
    <h2 class="text-center fw-bold mb-4">Daftar Destinasi Wisata Jawa Timur</h2>
    <div class="row g-4">
      <?php
        $destinasi = [
          ["Batu", "img/batu.webp"],
          ["Malang", "img/malang.webp"],
          ["Lumajang", "img/lumajang.webp"],
          ["Mojokerto", "img/mojokerto.jpeg"],
          ["Sumenep", "img/sumenep.jpg"],
          ["Probolinggo", "img/probolinggo.jpg"],
          ["Banyuwangi", "img/banyuwangi.jpeg"],
          ["Surabaya", "img/surabaya.jpg"],
        ];

        foreach ($destinasi as $d) {
          echo "
          <div class='col-md-3'>
            <a href='destinasi_detail.php?kota={$d[0]}' style='text-decoration:none; color:black;'>
              <div class='card h-100 shadow-sm'>
                <img src='{$d[1]}' class='card-img-top' style='height:250px; object-fit:cover;'>
                <div class='card-body text-center' style='background: #145C43; color: white;'>
                  <h5 class='fw-bold'>{$d[0]}</h5>
                  <button class='btn w-100 mt-2' style='background: #CDAA7D;'>Lihat Paket</button>
                </div>
              </div>
            </a>
          </div>";
        }
      ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>