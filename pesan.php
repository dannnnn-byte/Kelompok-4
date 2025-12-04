<?php 
include 'includes/header.php'; 
include 'includes/dashboard_home.php'; 
include 'includes/navbar.php'; 
include 'koneksi.php'; 
?>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">Form Pemesanan Tiket</h2>
  <div class="row justify-content-center">
    <div class="col-md-6">

      <?php
      if (isset($_POST['pesan'])) {
          $nama = $_POST['nama'];
          $wisata = $_POST['wisata'];
          $tanggal = $_POST['tanggal'];
          $jumlah = $_POST['jumlah'];
          $transportasi = $_POST['transportasi'];

          $query = "INSERT INTO pemesanan (nama, wisata, tanggal, jumlah, transportasi)
                    VALUES ('$nama', '$wisata', '$tanggal', '$jumlah', '$transportasi')";

          if (mysqli_query($conn, $query)) {
              echo "<div class='alert alert-success'>✅ Pemesanan berhasil! Kami akan menghubungi Anda untuk konfirmasi.</div>";
          } else {
              echo "<div class='alert alert-danger'>❌ Terjadi kesalahan: " . mysqli_error($conn) . "</div>";
          }
      }
      ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tujuan Wisata</label>
          <select name="wisata" class="form-select" required>
            <option value="">-- Pilih Destinasi --</option>
            <option>Gunung Bromo</option>
            <option>Pantai Balekambang</option>
            <option>Kawah Ijen</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal Keberangkatan</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jumlah Orang</label>
          <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Transportasi</label>
          <select name="transportasi" class="form-select" required>
            <option value="">-- Pilih Transportasi --</option>
            <option>Travel Van</option>
            <option>Bus Pariwisata</option>
            <option>Motor Sewa</option>
          </select>
        </div>
        <button type="submit" name="pesan" class="btn btn-success w-100">Pesan Sekarang</button>
      </form>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
