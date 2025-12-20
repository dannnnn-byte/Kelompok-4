<?php
session_start();
require_once '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($userId < 1) { header('Location: user_list.php'); exit; }

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['id_user'] ?? 0);
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $newPassword = $_POST['new_password'] ?? '';

    if ($nama === '' || $email === '') {
        $error = 'Nama dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // unique email
        $stmt = $conn->prepare('SELECT id_user FROM users WHERE email = ? AND id_user <> ? LIMIT 1');
        $stmt->bind_param('si', $email, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) { $error = 'Email sudah digunakan.'; }
        $stmt->close();

        if ($error === '') {
            if ($newPassword !== '') {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET nama_lengkap=?, email=?, alamat=?, role=?, password=? WHERE id_user=?');
                $stmt->bind_param('sssssi', $nama, $email, $alamat, $role, $hashed, $userId);
            } else {
                $stmt = $conn->prepare('UPDATE users SET nama_lengkap=?, email=?, alamat=?, role=? WHERE id_user=?');
                $stmt->bind_param('ssssi', $nama, $email, $alamat, $role, $userId);
            }
            if ($stmt && $stmt->execute()) {
                $success = 'User berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui user.';
            }
            if ($stmt) $stmt->close();
        }
    }
}

// fetch user
$stmt = $conn->prepare('SELECT id_user, nama_lengkap, email, alamat, role FROM users WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
$user = $res && $res->num_rows === 1 ? $res->fetch_assoc() : null;
$stmt->close();

include 'header_admin.php';
include 'sidebar_admin.php';
?>
<div class="container my-4">
  <h3 class="fw-bold mb-3">Edit Pengguna</h3>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($user): ?>
  <form method="POST" action="edit_user.php?id=<?= $userId ?>" class="card p-3 shadow-sm">
    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="user" <?= $user['role']==='user'?'selected':'' ?>>User</option>
          <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password Baru (opsional)</label>
        <input type="password" name="new_password" class="form-control" placeholder="Isi jika ingin mengubah password">
      </div>
      <div class="col-12">
        <label class="form-label">Alamat</label>
        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($user['alamat'] ?? '') ?>">
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan</button>
      <a href="user_list.php" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
  <?php else: ?>
  <div class="alert alert-warning">Pengguna tidak ditemukan.</div>
  <?php endif; ?>
</div>
<?php include 'footer_admin.php'; ?>
