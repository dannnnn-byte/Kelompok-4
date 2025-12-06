<?php
session_start();
include 'koneksi.php'; // koneksi ke database

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama    = trim($_POST['nama']);
    $alamat  = trim($_POST['alamat']);
    $email   = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    // Validasi password
    if ($password !== $password2) {
        $error = "Password tidak sama!";
    } else {

        // Cek apakah email sudah ada
        $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email sudah digunakan!";
        } else {

            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, alamat, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $alamat, $email, $hashed);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silahkan login.";
            } else {
                $error = "Terjadi kesalahan: " . $conn->error;
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/dashboard_home.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">

      <h3 class="text-center fw-bold mb-4">Daftar Akun JawaTrip</h3>

      <?php if (!empty($error)) : ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php endif; ?>

      <?php if (!empty($success)) : ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
      <?php endif; ?>

      <form action="" method="POST">

        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat Asal / Kota</label>
          <input type="text" name="alamat" class="form-control" placeholder="Contoh: Surabaya, Sidoarjo, Malang" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" name="password2" class="form-control" required>
        </div>

        <button type="submit" name="register" class="btn btn-primary w-100 mt-2">Daftar</button>

        <p class="text-center mt-3">
          Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
      </form>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
