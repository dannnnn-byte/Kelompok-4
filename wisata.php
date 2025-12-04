<?php
include 'includes/header.php';
include 'includes/dashboard_home.php';
include 'includes/navbar.php';
include 'koneksi.php';

// Ambil data wisata dari database
$wisata = [];
$query = "SELECT * FROM wisata";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        // setiap elemen array: [nama, lokasi, gambar, harga]
        $wisata[] = [$row['nama'], $row['lokasi'], $row['gambar'], $row['harga']];
    }
}
?>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">Daftar Destinasi Wisata Jawa Timur</h2>
  <div class="row g-4">
    <?php
      if(!empty($wisata)){
          foreach ($wisata as $w) {
              echo "
              <div class='col-md-3'>
                <div class='card h-100 shadow-sm'>
                  <img src='{$w[2]}' class='card-img-top'>
                  <div class='card-body'>
                    <h5>{$w[0]}</h5>
                    <p class='text-muted mb-1'>{$w[1]}</p>
                    <p class='fw-bold'>{$w[3]}</p>
                    <a href='pesan.php' class='btn btn-success w-100'>Pesan Tiket</a>
                  </div>
                </div>
              </div>";
          }
      } else {
          echo "<p class='text-center'>Belum ada destinasi wisata.</p>";
      }
    ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
