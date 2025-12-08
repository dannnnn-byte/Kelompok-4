<?php
session_start();
include 'koneksi.php'; 
include 'includes/header.php'; 
include 'includes/dashboard_home.php'; 
include 'includes/navbar.php'; 

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama      = trim($_POST['nama']);
    $alamat    = trim($_POST['alamat']); // <-- DITAMBAHKAN
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    // Validasi password
    if ($password !== $password2) {
        $error = "Password tidak sama!";
    } else {

        // Cek email sudah ada
        $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email sudah digunakan!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role_default = 'user';

            // INSERT sudah ditambahkan kolom alamat
            $stmt = $conn->prepare("
                INSERT INTO users (nama_lengkap, alamat, email, password, role) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $nama, $alamat, $email, $hashed, $role_default);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silahkan login.";
            } else {
                $error = "Registrasi GAGAL! Error Database: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi JawaTrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/dashboard_home.css">
</head>
<body>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-8">

      <div class="card shadow border-0">
        <div class="card-header bg-success text-white text-center py-3">
          <h4 class="fw-bold mb-0">Daftar Akun Baru</h4>
        </div>

        <div class="card-body p-4">

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

            <!-- INPUT ALAMAT -->
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <input type="text" name="alamat" class="form-control" required>
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

            <button type="submit" name="register" class="btn btn-success w-100 py-2 rounded-3 fw-semibold">
              DAFTAR
            </button>

            <p class="text-center mt-3 mb-0">
                Sudah punya akun?
                <a href="login.php" class="text-success fw-bold text-decoration-none">Login Sekarang</a>
            </p>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>
