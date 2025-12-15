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
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
