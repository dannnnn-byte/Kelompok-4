<?php 
include 'includes/header.php';
include 'includes/dashboard_home.php';
include 'includes/navbar.php';
include 'koneksi.php';
?>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">Form Pemesanan Hotel</h2>
  <div class="row justify-content-center">
    <div class="col-md-6">

      <?php
      if(isset($_POST['pesan_hotel'])){
          $nama = $_POST['nama'];
          $hotel = $_POST['hotel'];
          $checkin = $_POST['checkin'];
          $checkout = $_POST['checkout'];
          $jumlah_kamar = $_POST['jumlah_kamar'];
          $jumlah_orang = $_POST['jumlah_orang'];
          $transportasi = $_POST['transportasi'];

          $query = "INSERT INTO pemesanan_hotel 
                    (nama, hotel, tanggal_checkin, tanggal_checkout, jumlah_kamar, jumlah_orang, transportasi)
                    VALUES 
                    ('$nama', '$hotel', '$checkin', '$checkout', '$jumlah_kamar', '$jumlah_orang', '$transportasi')";

          if(mysqli_query($conn, $query)){
              echo "<div class='alert alert-success'>✅ Pemesanan hotel berhasil! Kami akan menghubungi Anda untuk konfirmasi.</div>";
          } else {
              echo "<div class='alert alert-danger'>❌ Terjadi kesalahan: ".mysqli_error($conn)."</div>";
          }
      }
      ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Pilih Hotel</label>
          <select name="hotel" class="form-select" required>
            <option value="">-- Pilih Hotel --</option>
            <option>Hotel Santika</option>
            <option>JW Marriott</option>
            <option>Golden Tulip</option>
            <option>Ketapang Indah</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal Check-in</label>
          <input type="date" name="checkin" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal Check-out</label>
          <input type="date" name="checkout" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Jumlah Kamar</label>
          <input type="number" name="jumlah_kamar" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Jumlah Orang</label>
          <input type="number" name="jumlah_orang" class="form-control" min="1" required>
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

        <button type="submit" name="pesan_hotel" class="btn btn-success w-100">Pesan Sekarang</button>
      </form>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
