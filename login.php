<?php
session_start();
require_once 'koneksi.php';

// Include komponen
include 'includes/navbar.php';
include 'includes/header.php';
include 'includes/dashboard_home.php';

$error = "";
$redirect = $_GET['redirect'] ?? '../index.php'; // Halaman tujuan setelah login

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $email    = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // LOGIN ADMIN
    $sql_admin = "SELECT * FROM admins WHERE username='$email' AND password='$password'";
    $admin_res = mysqli_query($conn, $sql_admin);
    if ($admin_res && mysqli_num_rows($admin_res) > 0) {
        $_SESSION['admin'] = $email;
        header("Location: admin/dashboard.php");
        exit;
    }

    // LOGIN USER
    $sql_user = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $user_res = mysqli_query($conn, $sql_user);

    if ($user_res && mysqli_num_rows($user_res) > 0) {
        $user = mysqli_fetch_assoc($user_res);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['nama'];
        $_SESSION['email']    = $user['email'];

        header("Location: $redirect");
        exit;
    }

    $error = "Email atau password yang Anda masukkan salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login JawaTrip</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../assets/dashboard_home.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">

            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="fw-bold mb-0">Masuk ke JawaTrip</h4>
                </div>

                <div class="card-body p-4">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email / Username Admin</label>
                            <input type="text" name="email" class="form-control rounded-3" placeholder="Masukkan email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Masukkan password" required>
                        </div>

                        <button type="submit" name="login" class="btn btn-success w-100 py-2 rounded-3 fw-semibold">
                            Login
                        </button>

                        <p class="text-center mt-3 mb-0">
                            Belum punya akun?
                            <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar Sekarang</a>
                        </p>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
