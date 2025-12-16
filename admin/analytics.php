<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

$nama_admin = $_SESSION['nama'] ?? $_SESSION['email'] ?? 'Admin';

// ================= STATISTICS =================
$stats = [
    'total_pemesanan' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan"))['total'],
    'total_pendapatan' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM pemesanan"))['total'] ?? 0,
    'total_users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'],
    'pending_payment' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan WHERE status_bayar = 'pending'"))['total'],
];

// ================= CHART DATA =================
// Monthly Revenue
$monthly_revenue = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM pemesanan WHERE DATE_FORMAT(tanggal_pesan, '%Y-%m') = '$month'"))['total'] ?? 0;
    $monthly_revenue[] = ['month' => date('M Y', strtotime($month)), 'revenue' => $revenue];
}

// Top Destinations
$top_destinations = [];
$query = "SELECT k.nama_kota, COUNT(p.id_pemesanan) as total_booking, SUM(p.total_bayar) as revenue
          FROM pemesanan p
          JOIN paket_wisata pw ON p.id_paket = pw.id_paket
          JOIN kota k ON pw.id_kota = k.id_kota
          GROUP BY k.nama_kota
          ORDER BY total_booking DESC
          LIMIT 5";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $top_destinations[] = $row;
}

// Payment Status Distribution
$payment_status = [];
$statuses = ['pending', 'paid', 'lunas', 'canceled'];
foreach ($statuses as $status) {
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan WHERE status_bayar = '$status'"))['total'];
    if ($count > 0) {
        $payment_status[] = ['status' => ucfirst($status), 'count' => $count];
    }
}
?>

<style>
.analytics-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0;
    color: white;
    margin-bottom: 40px;
}
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 15px;
}
.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #333;
    margin-bottom: 5px;
}
.stat-label {
    color: #666;
    font-size: 0.95rem;
}
.chart-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}
.chart-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 25px;
    color: #333;
}
.table-rank {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
}
.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); }
.rank-other { background: linear-gradient(135deg, #667eea, #764ba2); }
</style>

<section class="analytics-hero">
    <div class="container">
        <h1 class="fw-bold mb-2">📊 Analytics Dashboard</h1>
        <p class="mb-0">Data analitik dan statistik bisnis real-time</p>
    </div>
</section>

<div class="container pb-5">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-value"><?= $stats['total_pemesanan'] ?></div>
                <div class="stat-label">Total Pemesanan</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-value">Rp<?= number_format($stats['total_pendapatan'] / 1000000, 1) ?>jt</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-value"><?= $stats['pending_payment'] ?></div>
                <div class="stat-label">Pending Pembayaran</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="chart-card">
                <h3 class="chart-title">📈 Pendapatan 12 Bulan Terakhir</h3>
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>

        <!-- Payment Status Chart -->
        <div class="col-lg-4 mb-4">
            <div class="chart-card">
                <h3 class="chart-title">💳 Status Pembayaran</h3>
                <canvas id="paymentStatusChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Destinations Table -->
    <div class="chart-card">
        <h3 class="chart-title">🏆 Top 5 Destinasi Populer</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">Rank</th>
                        <th>Destinasi</th>
                        <th class="text-end">Total Booking</th>
                        <th class="text-end">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($top_destinations as $dest): ?>
                    <tr>
                        <td>
                            <div class="table-rank rank-<?= $rank <= 3 ? $rank : 'other' ?>">
                                <?= $rank ?>
                            </div>
                        </td>
                        <td class="fw-bold"><?= $dest['nama_kota'] ?></td>
                        <td class="text-end"><?= $dest['total_booking'] ?> booking</td>
                        <td class="text-end fw-bold text-success">Rp<?= number_format($dest['revenue'], 0, ',', '.') ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthly_revenue, 'month')) ?>,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: <?= json_encode(array_column($monthly_revenue, 'revenue')) ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderColor: '#667eea',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                    }
                }
            }
        }
    }
});

// Payment Status Chart
const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($payment_status, 'status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($payment_status, 'count')) ?>,
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(102, 126, 234, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12, weight: 'bold' }
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
