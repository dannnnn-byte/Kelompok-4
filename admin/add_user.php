<?php
session_start();
require_once '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Nama, email, dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Unique email
        $stmt = $conn->prepare('SELECT id_user FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $error = 'Email sudah digunakan.';
        }
        $stmt->close();

        if ($error === '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (nama_lengkap, email, password, alamat, role) VALUES (?,?,?,?,?)');
            $stmt->bind_param('sssss', $nama, $email, $hashed, $alamat, $role);
            if ($stmt->execute()) {
                $success = 'User berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan user.';
            }
            $stmt->close();
        }
    }
}

include 'header_admin.php';
include 'sidebar_admin.php';
?>
<div class="container my-4">
  <h3 class="fw-bold mb-3">Tambah Pengguna</h3>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST" action="add_user.php" class="card p-3 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Alamat</label>
        <input type="text" name="alamat" class="form-control">
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan</button>
      <a href="user_list.php" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php include 'footer_admin.php'; ?>
