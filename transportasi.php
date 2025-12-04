<?php include 'includes/header.php'; ?>
<?php include 'includes/dashboard_home.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">Pilihan Transportasi Wisata</h2>
  <div class="row g-4">
    <?php
      $kendaraan = [
        ["Travel Van", "6 Penumpang", "Rp500.000/hari", "img/bus.jpeg"],
        ["Bus Pariwisata", "30 Penumpang", "Rp1.200.000/hari", "img/bus.jpeg"],
        ["Motor Sewa", "1 Penumpang", "Rp150.000/hari", "img/bus.jpeg"]
      ];

      foreach ($kendaraan as $k) {
        echo "
        <div class='col-md-4'>
          <div class='card shadow-sm h-100'>
            <img src='{$k[3]}' class='card-img-top'>
            <div class='card-body'>
              <h5>{$k[0]}</h5>
              <p class='text-muted'>{$k[1]}</p>
              <p class='fw-bold'>{$k[2]}</p>
              <a href='pesan.php' class='btn btn-success w-100'>Sewa Sekarang</a>
            </div>
          </div>
        </div>";
      }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
