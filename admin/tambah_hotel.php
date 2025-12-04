<?php
include '../koneksi.php';

if(isset($_POST['submit'])){
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];

    // Upload gambar
    $target_dir = "../img/";
    $gambar_file = $_FILES['gambar']['name'];
    $target_file = $target_dir . basename($gambar_file);

    if(move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)){
        $query = "INSERT INTO hotel (nama, lokasi, harga, gambar) 
                  VALUES ('$nama', '$lokasi', '$harga', 'img/$gambar_file')";
        if(mysqli_query($conn, $query)){
            $success = "Hotel berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan hotel: " . mysqli_error($conn);
        }
    } else {
        $error = "Gagal mengupload gambar.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
  <h2 class="mb-4">Tambah Hotel Baru</h2>
  
  <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
  <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Nama Hotel</label>
      <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Lokasi</label>
      <input type="text" name="lokasi" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Harga</label>
      <input type="text" name="harga" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Gambar</label>
      <input type="file" name="gambar" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-success">Tambah Hotel</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
