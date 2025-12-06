<?php
session_start();
// Pastikan path ke koneksi.php benar
require_once 'koneksi.php';

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// HAPUS include komponen TAMPILAN di atas logika PHP! Ini MENGATASI error "Cannot modify header information"
// include 'includes/navbar.php'; 
// include 'includes/header.php';

$error = "";
$redirect = $_GET['redirect'] ?? '../index.php'; 
$identifier = ""; // Digunakan untuk menampung input email
$table_name = "users"; // Nama tabel gabungan Admin dan User (sesuai data Anda)

if (isset($_POST['login'])) {
    $identifier = trim($_POST['email']); 
    $password_input = $_POST['password']; 

    if (empty($identifier) || empty($password_input)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $user_found = false;

        // --- PROSES LOGIN AMAN (Menggunakan Prepared Statement) ---
        // Mencari pengguna berdasarkan 'email'. Kolom disesuaikan dengan struktur database Anda.
        $sql = "SELECT id_user, nama_lengkap, email, password, role FROM {$table_name} WHERE email = ?";
        
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // VERIFIKASI PASSWORD (Membutuhkan password di DB sudah dalam bentuk hash)
                if (password_verify($password_input, $user['password'])) {
                    $user_found = true;
                    
                    // Cek Role dan Redirect
                    if ($user['role'] === 'admin') {
                        // SET SESSION ADMIN
                        $_SESSION['admin_id'] = $user['id_user'];
                        $_SESSION['admin_username'] = $user['nama_lengkap'];
                        $_SESSION['admin'] = $user['email']; 
                        
                        header("Location: admin/dashboard.php");
                        exit;
                    } else if ($user['role'] === 'user') {
                        // SET SESSION USER
                        $_SESSION['user_id']    = $user['id_user'];
                        $_SESSION['username']   = $user['nama_lengkap'];
                        $_SESSION['email']      = $user['email'];

                        header("Location: " . $redirect);
                        exit;
                    }
                }
            }
            $stmt->close();
        }
        
        if (!$user_found) {
            $error = "Email atau password yang Anda masukkan salah!";
        }
    }
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

<?php 
include 'includes/navbar.php'; 
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">

            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="fw-bold mb-0">Masuk ke JawaTrip</h4>
                </div>

                <div class="card-body p-4">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="text" name="email" class="form-control rounded-3" placeholder="Masukkan email" required value="<?= htmlspecialchars($identifier) ?>">
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