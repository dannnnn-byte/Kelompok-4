<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Ambil data user
$user = null;
$successMsg = '';
$errorMsg = '';

// Handle update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if ($nama === '' || $email === '') {
        $errorMsg = 'Nama dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Format email tidak valid.';
    } else {
        // Cek email unik
        $stmt = $conn->prepare('SELECT id_user FROM users WHERE email = ? AND id_user <> ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('si', $email, $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows > 0) {
                $errorMsg = 'Email sudah digunakan oleh pengguna lain.';
            }
            $stmt->close();
        }

        if ($errorMsg === '') {
            if ($newPassword !== '') {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET nama_lengkap = ?, email = ?, alamat = ?, password = ? WHERE id_user = ?');
                $stmt->bind_param('ssssi', $nama, $email, $alamat, $hashed, $userId);
            } else {
                $stmt = $conn->prepare('UPDATE users SET nama_lengkap = ?, email = ?, alamat = ? WHERE id_user = ?');
                $stmt->bind_param('sssi', $nama, $email, $alamat, $userId);
            }
            if ($stmt && $stmt->execute()) {
                $successMsg = 'Profil berhasil diperbarui.';
                $_SESSION['username'] = $nama;
                $_SESSION['email'] = $email;
            } else {
                $errorMsg = 'Gagal memperbarui profil.';
            }
            if ($stmt) $stmt->close();
        }
    }
}

// Handle delete account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_account') {
    // Optional: prevent deleting admin here (normal users only reach this page)
    $stmt = $conn->prepare('DELETE FROM users WHERE id_user = ?');
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            // Logout
            session_unset();
            session_destroy();
            header('Location: register.php');
            exit;
        } else {
            $errorMsg = 'Gagal menghapus akun.';
        }
        $stmt->close();
    }
}

// Ambil data terbaru setelah update
$stmt = $conn->prepare('SELECT id_user, nama_lengkap, email, alamat, role FROM users WHERE id_user = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
    }
    $stmt->close();
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<link rel="stylesheet" href="assets/dashboard_home.css">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Profil Saya</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
                    <?php endif; ?>
                    <?php if ($user): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <div class="fw-bold"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <div class="fw-bold"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <div class="fw-bold"><?= htmlspecialchars($user['alamat'] ?? '-') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Role</label>
                            <span class="badge bg-secondary"><?= htmlspecialchars($user['role'] ?? 'user') ?></span>
                        </div>

                        <hr>
                        <button class="btn btn-outline-success mb-3" type="button" onclick="document.getElementById('editForm').classList.toggle('d-none');">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </button>

                        <form id="editForm" class="d-none" method="POST" action="profil.php">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($user['alamat'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Password Baru (opsional)</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Isi jika ingin mengubah password">
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editForm').classList.add('d-none');">Batal</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Data profil tidak ditemukan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3">
                <a href="riwayat.php" class="btn btn-outline-success">
                    <i class="bi bi-clock-history"></i> Lihat Riwayat Pesanan
                </a>
                <form method="POST" action="profil.php" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.');">
                    <input type="hidden" name="action" value="delete_account">
                    <button type="submit" class="btn btn-outline-danger ms-2">
                        <i class="bi bi-trash"></i> Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
