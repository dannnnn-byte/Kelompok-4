<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

// Handle Actions earlier to avoid HTML in JSON responses
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add') {
        $kode = mysqli_real_escape_string($conn, $_POST['kode_promo']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama_promo']);
        $jenis = $_POST['jenis_diskon'];
        $nilai = $_POST['nilai_diskon'];
        $min_transaksi = $_POST['min_transaksi'];
        $max_diskon = $_POST['max_diskon'] ?? 0;
        $kuota = $_POST['kuota'];
        $tgl_mulai = $_POST['tanggal_mulai'];
        $tgl_selesai = $_POST['tanggal_selesai'];
        
        mysqli_query($conn, "INSERT INTO promo_diskon (kode_promo, nama_promo, jenis_diskon, nilai_diskon, min_transaksi, max_diskon, kuota, tanggal_mulai, tanggal_selesai, status) 
                            VALUES ('$kode', '$nama', '$jenis', '$nilai', '$min_transaksi', '$max_diskon', '$kuota', '$tgl_mulai', '$tgl_selesai', 'active')");
        
        $_SESSION['success'] = "Promo berhasil ditambahkan!";
        header("Location: promo_management.php");
        exit;
    }
    
    if ($action == 'delete') {
        $id = $_POST['id'];
        mysqli_query($conn, "DELETE FROM promo_diskon WHERE id = '$id'");
        $_SESSION['success'] = "Promo berhasil dihapus!";
        header("Location: promo_management.php");
        exit;
    }
    
    if ($action == 'toggle_status') {
        $id = $_POST['id'];
        $status = $_POST['status'] == 'active' ? 'inactive' : 'active';
        mysqli_query($conn, "UPDATE promo_diskon SET status = '$status' WHERE id = '$id'");
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'status' => $status]);
        exit;
    }
}

include '../includes/header.php';
include '../includes/navbar.php';

// Get all promos
$query = "SELECT * FROM promo_diskon ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<style>
.promo-hero {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 40px 0;
    color: white;
    margin-bottom: 40px;
}
.promo-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}
.promo-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.promo-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);
}
.promo-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
}
.badge-active {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}
.badge-inactive {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}
.badge-expired {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}
.promo-code {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 1.3rem;
    letter-spacing: 2px;
    display: inline-block;
    margin-bottom: 15px;
}
.countdown-timer {
    background: rgba(255, 193, 7, 0.1);
    color: #ff6b6b;
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.usage-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin: 10px 0;
}
.usage-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    transition: width 0.5s;
}
.toggle-switch {
    position: relative;
    width: 60px;
    height: 30px;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 30px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
input:checked + .toggle-slider {
    background-color: #28a745;
}
input:checked + .toggle-slider:before {
    transform: translateX(30px);
}
</style>

<section class="promo-hero">
    <div class="container">
        <h1 class="fw-bold mb-2">🎁 Manajemen Promo & Diskon</h1>
        <p class="mb-0">Kelola voucher dan diskon untuk meningkatkan penjualan</p>
    </div>
</section>

<div class="container pb-5">
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Promo Aktif</h4>
            <p class="text-muted mb-0"><?= mysqli_num_rows($result) ?> promo tersedia</p>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addPromoModal">
            <i class="bi bi-plus-circle"></i> Tambah Promo Baru
        </button>
    </div>

    <div class="row">
        <?php while ($promo = mysqli_fetch_assoc($result)):
            $now = new DateTime();
            $end = new DateTime($promo['tanggal_selesai']);
            $is_expired = $end < $now;
            $used = isset($promo['terpakai']) ? (int)$promo['terpakai'] : 0;
            $kuota = isset($promo['kuota']) ? (int)$promo['kuota'] : 0;
            $usage_percent = $kuota > 0 ? min(100, ($used / $kuota) * 100) : 0;
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="promo-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="promo-code"><?= $promo['kode_promo'] ?></div>
                    <label class="toggle-switch">
                        <input type="checkbox" <?= $promo['status'] == 'active' ? 'checked' : '' ?>
                               onchange="toggleStatus(<?= $promo['id'] ?>, '<?= $promo['status'] ?>')">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <h5 class="fw-bold mb-3"><?= $promo['nama_promo'] ?></h5>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Diskon:</span>
                        <span class="fw-bold text-success fs-5">
                            <?= $promo['jenis_diskon'] == 'percentage' ? $promo['nilai_diskon'] . '%' : 'Rp' . number_format($promo['nilai_diskon'], 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php if ($promo['min_transaksi'] > 0): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Min. Transaksi:</span>
                        <span class="fw-semibold">Rp<?= number_format($promo['min_transaksi'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Kuota Tersisa:</small>
                    <div class="usage-bar">
                        <div class="usage-bar-fill" style="width: <?= $usage_percent ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $used ?> / <?= $kuota ?> digunakan</small>
                </div>

                <div class="mb-3">
                    <div class="countdown-timer" data-end="<?= $promo['tanggal_selesai'] ?>">
                        <i class="bi bi-clock"></i>
                        <span class="countdown-text">Loading...</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletePromo(<?= $promo['id'] ?>)">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Promo Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">🎁 Tambah Promo Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode Promo *</label>
                            <input type="text" class="form-control" name="kode_promo" placeholder="DISKON50" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Promo *</label>
                            <input type="text" class="form-control" name="nama_promo" placeholder="Diskon Tahun Baru" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Diskon *</label>
                            <select class="form-select" name="jenis_diskon" required>
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nilai Diskon *</label>
                            <input type="number" class="form-control" name="nilai_diskon" placeholder="50" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Min. Transaksi</label>
                            <input type="number" class="form-control" name="min_transaksi" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Max. Diskon (opsional)</label>
                            <input type="number" class="form-control" name="max_diskon" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Kuota *</label>
                            <input type="number" class="form-control" name="kuota" value="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai *</label>
                            <input type="datetime-local" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai *</label>
                            <input type="datetime-local" class="form-control" name="tanggal_selesai" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                        <i class="bi bi-plus-circle"></i> Buat Promo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timers
document.querySelectorAll('.countdown-timer').forEach(timer => {
    const endDate = new Date(timer.dataset.end);
    
    function updateCountdown() {
        const now = new Date();
        const diff = endDate - now;
        
        if (diff <= 0) {
            timer.querySelector('.countdown-text').textContent = 'Berakhir';
            timer.style.background = 'rgba(220, 53, 69, 0.1)';
            return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        timer.querySelector('.countdown-text').textContent = `${days}h ${hours}j ${minutes}m`;
    }
    
    updateCountdown();
    setInterval(updateCountdown, 60000);
});

function toggleStatus(id, currentStatus) {
    fetch('promo_management.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle_status&id=${id}&status=${currentStatus}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deletePromo(id) {
    if (!confirm('Yakin ingin menghapus promo ini?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include '../includes/footer.php'; ?>
