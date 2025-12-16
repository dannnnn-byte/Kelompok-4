<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../includes/header.php';
include '../includes/navbar.php';

/* ================= AMBIL DATA PEMESANAN ================= */
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_pemesanan = mysqli_real_escape_string($conn, $_GET['id']);

// Query data pemesanan dan penumpang
$query = "
SELECT 
    p.id_pemesanan,
    p.kode_booking,
    p.tgl_tour,
    p.jumlah_peserta,
    p.jumlah_dewasa,
    p.jumlah_anak,
    p.total_bayar,
    p.status_bayar,
    p.tanggal_pesan,
    pk.nama_paket,
    pk.harga_per_pax,
    k.nama_kota
FROM pemesanan p
JOIN paket_wisata pk ON p.id_paket = pk.id_paket
JOIN kota k ON pk.id_kota = k.id_kota
WHERE p.id_pemesanan = '$id_pemesanan'
";

$result = mysqli_query($conn, $query);
$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    echo "<div class='alert alert-danger'>Pemesanan tidak ditemukan</div>";
    exit;
}

// Query data penumpang
$query_penumpang = "SELECT * FROM penumpang WHERE id_pemesanan = '$id_pemesanan' ORDER BY id_penumpang ASC";
$result_penumpang = mysqli_query($conn, $query_penumpang);
$penumpangs = [];
while ($p = mysqli_fetch_assoc($result_penumpang)) {
    $penumpangs[] = $p;
}

/* ================= PROSES UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_edit'])) {
    mysqli_begin_transaction($conn);
    
    try {
        // Update data penumpang
        foreach ($_POST['penumpang'] as $id_penumpang => $data) {
            $nama = mysqli_real_escape_string($conn, $data['nama']);
            $email = mysqli_real_escape_string($conn, $data['email']);
            $telepon = mysqli_real_escape_string($conn, $data['telepon']);
            $alamat = mysqli_real_escape_string($conn, $data['alamat']);
            $identitas = mysqli_real_escape_string($conn, $data['identitas']);
            
            $query_update = "UPDATE penumpang SET 
                nama_lengkap = '$nama',
                email = '$email',
                no_telepon = '$telepon',
                alamat = '$alamat',
                no_identitas = '$identitas'
            WHERE id_penumpang = '$id_penumpang'";
            
            if (!mysqli_query($conn, $query_update)) {
                throw new Exception("Error update penumpang: " . mysqli_error($conn));
            }
        }
        
        // Insert log aktivitas
        $query_log = "INSERT INTO booking_log (id_pemesanan, aktivitas, keterangan) 
                     VALUES ('$id_pemesanan', 'Data Diperbarui', 'Admin memperbarui data penumpang')";
        mysqli_query($conn, $query_log);
        
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Data pemesanan berhasil diperbarui";
        header("Location: dashboard.php");
        exit;
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemesanan - Admin JawaTrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .edit-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .passenger-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #0d6efd;
        }
        .passenger-title {
            font-weight: 600;
            color: #0d6efd;
            margin-bottom: 15px;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .btn-group-edit {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="container edit-container">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div>
                <h2 class="mb-0">Edit Pemesanan</h2>
                <p class="text-muted mb-0">Kode: <strong><?= htmlspecialchars($pemesanan['kode_booking']) ?></strong></p>
            </div>
        </div>

        <!-- Info Pemesanan -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Paket:</strong> <?= htmlspecialchars($pemesanan['nama_paket']) ?></p>
                        <p class="mb-2"><strong>Kota:</strong> <?= htmlspecialchars($pemesanan['nama_kota']) ?></p>
                        <p class="mb-0"><strong>Tanggal Tour:</strong> <?= date('d M Y', strtotime($pemesanan['tgl_tour'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Peserta:</strong> <?= $pemesanan['jumlah_peserta'] ?> Orang (<?= $pemesanan['jumlah_dewasa'] ?> Dewasa, <?= $pemesanan['jumlah_anak'] ?> Anak)</p>
                        <p class="mb-2"><strong>Total Bayar:</strong> Rp <?= number_format($pemesanan['total_bayar'], 0, ',', '.') ?></p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge <?= $pemesanan['status_bayar'] === 'lunas' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $pemesanan['status_bayar'] ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
        </div>
        <?php endif; ?>

        <!-- Form Edit Data Penumpang -->
        <form method="POST">
            <input type="hidden" name="submit_edit" value="1">
            
            <?php foreach ($penumpangs as $index => $penumpang): ?>
            <div class="passenger-card">
                <h5 class="passenger-title">
                    <i class="bi bi-person"></i> 
                    Penumpang <?= $index + 1 ?> 
                    <span class="badge bg-primary"><?= ucfirst($penumpang['tipe_penumpang']) ?></span>
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="penumpang[<?= $penumpang['id_penumpang'] ?>][nama]" 
                                   value="<?= htmlspecialchars($penumpang['nama_lengkap']) ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   name="penumpang[<?= $penumpang['id_penumpang'] ?>][email]" 
                                   value="<?= htmlspecialchars($penumpang['email']) ?>"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" 
                                   class="form-control" 
                                   name="penumpang[<?= $penumpang['id_penumpang'] ?>][telepon]" 
                                   value="<?= htmlspecialchars($penumpang['no_telepon']) ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. Identitas (KTP/Paspor)</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="penumpang[<?= $penumpang['id_penumpang'] ?>][identitas]" 
                                   value="<?= htmlspecialchars($penumpang['no_identitas']) ?>"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" 
                              name="penumpang[<?= $penumpang['id_penumpang'] ?>][alamat]" 
                              rows="3"
                              required><?= htmlspecialchars($penumpang['alamat']) ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="btn-group-edit">
                <a href="dashboard.php" class="btn btn-secondary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary fw-bold" style="padding: 8px 16px; border-radius: 5px;">
                    <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
