<?php
session_start();
include 'koneksi.php'; // koneksi database

$error = "";

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Cek di tabel admins
    $query_admin = "SELECT * FROM admins WHERE username='$email' AND password='$password'";
    $result_admin = mysqli_query($conn, $query_admin);

    if(mysqli_num_rows($result_admin) > 0){
        $_SESSION['admin'] = $email;
        header("Location: admin/dashboard.php");
        exit;
    }

    // Cek di tabel users
    $query_user = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result_user = mysqli_query($conn, $query_user);

    if(mysqli_num_rows($result_user) > 0){
        $user = mysqli_fetch_assoc($result_user);
        $_SESSION['user'] = $user['email'];
        $_SESSION['nama'] = $user['nama'];
        header("Location: index.php");
        exit;
    }

    // Jika login gagal
    $error = "Email atau password salah!";
}

// Include header dan dashboard
include 'includes/header.php';
include 'includes/dashboard_home.php';
include 'includes/navbar.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h3 class="text-center fw-bold mb-4">Login JawaTrip</h3>
      
      <?php if(!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="text" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" name="login" class="btn btn-success w-100">Login</button>
        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
