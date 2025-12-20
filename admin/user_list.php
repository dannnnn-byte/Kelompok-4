<?php
session_start();
require_once '../koneksi.php';

// Admin-only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include 'header_admin.php';
include 'sidebar_admin.php';
?>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Manajemen Pengguna</h3>
    <a href="add_user.php" class="btn btn-success"><i class="bi bi-person-plus"></i> Tambah User</a>
  </div>

  <?php
  $result = $conn->query("SELECT id_user, nama_lengkap, email, alamat, role FROM users ORDER BY id_user ASC");
  ?>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Alamat</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($u = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($u['id_user']) ?></td>
              <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['alamat'] ?? '-') ?></td>
              <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'primary' : 'secondary' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
              <td>
                <a href="edit_user.php?id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i> Edit</a>
                <a href="delete_user.php?id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?');"><i class="bi bi-trash"></i> Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted">Belum ada data pengguna.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer_admin.php'; ?>
