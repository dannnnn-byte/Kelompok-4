<?php 
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/dashboard_home.php';
include 'koneksi.php';
?>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">Daftar Hotel & Penginapan</h2>
  <div class="row g-4">
    <?php
      // Array hotel statis
      $hotel = [
        ["Hotel Santika", "Malang", "Rp450.000/malam", "img/hotel.jpg"],
        ["JW Marriott", "Surabaya", "Rp900.000/malam", "img/hotel.jpg"],
        ["Golden Tulip", "Batu", "Rp600.000/malam", "img/hotel.jpg"],
        ["Ketapang Indah", "Banyuwangi", "Rp550.000/malam", "img/hotel.jpg"]
      ];

      // Tampilkan setiap hotel
      foreach ($hotel as $h) {
          echo "
          <div class='col-md-3'>
            <div class='card shadow-sm'>
              <img src='{$h[3]}' class='card-img-top'>
              <div class='card-body'>
                <h5>{$h[0]}</h5>
                <p class='text-muted'>{$h[1]}</p>
                <p><strong>{$h[2]}</strong></p>
                <a href='pesan_hotel.php?hotel={$h[0]}' class='btn btn-outline-success btn-sm w-100'>Pesan</a>
              </div>
            </div>
          </div>";
      }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
