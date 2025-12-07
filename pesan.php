<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Ambil data dari URL
$id_paket = isset($_GET['id_paket']) ? $_GET['id_paket'] : 1;

// Query data paket
$query_paket = "SELECT p.*, k.nama_kota 
                FROM paket_wisata p 
                JOIN kota k ON p.id_kota = k.id_kota 
                WHERE p.id_paket = '$id_paket'";
$result_paket = mysqli_query($conn, $query_paket);
$paket = mysqli_fetch_assoc($result_paket);

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_detail'])) {
    $_SESSION['booking'] = [
        'id_paket' => $id_paket,
        'tanggal' => $_POST['tanggal'],
        'dewasa' => $_POST['dewasa'],
        'anak' => $_POST['anak'],
        'total_penumpang' => $_POST['dewasa'] + $_POST['anak']
    ];
    header("Location: pesan_step2.php?id_paket=$id_paket");
    exit;
}
?>

<link rel="stylesheet" href="assets/booking-style.css">

<div class="booking-container">
    <div class="booking-header">
        <h1 class="booking-title">Booking Wisata</h1>
        <p class="booking-subtitle">Lengkapi data pemesanan Anda dengan mudah</p>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-step active">
            <div class="step-circle">1</div>
            <span class="step-label">Detail Pemesanan</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-circle">2</div>
            <span class="step-label">Data Penumpang</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-circle">3</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <!-- STEP 1: DETAIL PEMESANAN -->
    <div class="booking-grid">
        <!-- Sidebar Paket Info -->
        <div class="sidebar-info">
            <div class="paket-card">
                <img src="img/<?= $paket['gambar_paket'] ?>" alt="<?= $paket['nama_paket'] ?>" class="paket-img">
                <div class="paket-content">
                    <span class="badge-open">OPEN TRIP</span>
                    <h3 class="paket-name"><?= $paket['nama_paket'] ?></h3>
                    <div class="paket-info-item">
                        <i class="bi bi-geo-alt"></i>
                        <span><?= $paket['nama_kota'] ?></span>
                    </div>
                    <div class="paket-info-item">
                        <i class="bi bi-clock"></i>
                        <span><?= $paket['durasi'] ?></span>
                    </div>
                    <div class="paket-price">
                        <p class="price-label">Harga Per Orang</p>
                        <p class="price-amount">Rp <?= number_format($paket['harga_per_pax'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Detail Pemesanan -->
        <div class="form-section">
            <div class="form-card">
                <h2 class="form-title">
                    <i class="bi bi-calendar-check"></i>
                    Detail Pemesanan
                </h2>

                <form method="POST" id="formDetail">
                    <input type="hidden" name="submit_detail" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Tanggal Keberangkatan <span class="required">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required 
                               min="<?= date('Y-m-d') ?>"
                               value="<?= isset($_SESSION['booking']['tanggal']) ? $_SESSION['booking']['tanggal'] : '' ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah Dewasa <span class="required">*</span></label>
                            <div class="counter-group">
                                <button type="button" class="btn-counter" onclick="decreaseValue('dewasa')">-</button>
                                <input type="number" id="dewasa" name="dewasa" 
                                       value="<?= isset($_SESSION['booking']['dewasa']) ? $_SESSION['booking']['dewasa'] : 1 ?>" 
                                       min="1" class="counter-input" onchange="calculateTotal()">
                                <button type="button" class="btn-counter" onclick="increaseValue('dewasa')">+</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jumlah Anak (Diskon 30%)</label>
                            <div class="counter-group">
                                <button type="button" class="btn-counter" onclick="decreaseValue('anak')">-</button>
                                <input type="number" id="anak" name="anak" 
                                       value="<?= isset($_SESSION['booking']['anak']) ? $_SESSION['booking']['anak'] : 0 ?>" 
                                       min="0" class="counter-input" onchange="calculateTotal()">
                                <button type="button" class="btn-counter" onclick="increaseValue('anak')">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Biaya -->
                    <div class="summary-box">
                        <h3 class="summary-title">Ringkasan Biaya</h3>
                        <div class="summary-item">
                            <span id="labelDewasa">1 Dewasa × Rp <?= number_format($paket['harga_per_pax'], 0, ',', '.') ?></span>
                            <span id="totalDewasa" class="summary-price">Rp <?= number_format($paket['harga_per_pax'], 0, ',', '.') ?></span>
                        </div>
                        <div class="summary-item" id="rowAnak" style="display: none;">
                            <span id="labelAnak">0 Anak × Rp <?= number_format($paket['harga_per_pax'] * 0.7, 0, ',', '.') ?></span>
                            <span id="totalAnak" class="summary-price">Rp 0</span>
                        </div>
                        <div class="summary-total">
                            <span class="total-label">Total Pembayaran</span>
                            <span id="grandTotal" class="total-amount">Rp <?= number_format($paket['harga_per_pax'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Lanjutkan ke Data Penumpang
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Booking -->
<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <i class="bi bi-info-circle" style="font-size: 3rem; color: #145C43;"></i>
            <h3>Konfirmasi Booking</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin melanjutkan pemesanan?</p>
            <div class="confirm-details">
                <div class="detail-row">
                    <span class="label">Paket:</span>
                    <span class="value"><?= $paket['nama_paket'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Tanggal:</span>
                    <span class="value" id="confirmTanggal">-</span>
                </div>
                <div class="detail-row">
                    <span class="label">Jumlah:</span>
                    <span class="value" id="confirmJumlah">-</span>
                </div>
                <div class="detail-row">
                    <span class="label">Total:</span>
                    <span class="value total" id="confirmTotal">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal()" class="btn-cancel">
                <i class="bi bi-x-circle"></i> Batal
            </button>
            <button type="button" onclick="confirmBooking()" class="btn-confirm">
                <i class="bi bi-check-circle"></i> Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s;
}

.modal-content {
    background: white;
    border-radius: 20px;
    padding: 0;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s;
}

.modal-header {
    text-align: center;
    padding: 30px 20px 20px;
    border-bottom: 2px solid #f3f4f6;
}

.modal-header h3 {
    margin: 15px 0 0 0;
    color: #1f2937;
    font-size: 1.5rem;
}

.modal-body {
    padding: 25px 30px;
}

.modal-body > p {
    text-align: center;
    color: #6b7280;
    margin-bottom: 20px;
    font-size: 1.05rem;
}

.confirm-details {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-row:last-child {
    border-bottom: none;
    margin-top: 10px;
    padding-top: 15px;
    border-top: 2px solid #145C43;
}

.detail-row .label {
    color: #6b7280;
    font-weight: 600;
}

.detail-row .value {
    color: #1f2937;
    font-weight: 700;
}

.detail-row .value.total {
    color: #145C43;
    font-size: 1.3rem;
}

.modal-footer {
    padding: 20px 30px 30px;
    display: flex;
    gap: 15px;
}

.btn-cancel, .btn-confirm {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-cancel {
    background: #f3f4f6;
    color: #6b7280;
}

.btn-cancel:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.btn-confirm {
    background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(20, 92, 67, 0.3);
}

.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(20, 92, 67, 0.4);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(50px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

    <script>
    const hargaDewasa = <?= $paket['harga_per_pax'] ?>;
    const hargaAnak = <?= $paket['harga_per_pax'] * 0.7 ?>;

    // Prevent default form submit
    document.getElementById('formDetail').addEventListener('submit', function(e) {
        e.preventDefault();
        showConfirmModal();
    });

    function showConfirmModal() {
        const tanggal = document.querySelector('input[name="tanggal"]').value;
        const dewasa = parseInt(document.getElementById('dewasa').value);
        const anak = parseInt(document.getElementById('anak').value);
        
        // Validasi
        if (!tanggal) {
            alert('⚠️ Mohon pilih tanggal keberangkatan terlebih dahulu!');
            return;
        }
        
        // Format tanggal
        const date = new Date(tanggal);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = date.toLocaleDateString('id-ID', options);
        
        // Update modal content
        document.getElementById('confirmTanggal').textContent = formattedDate;
        document.getElementById('confirmJumlah').textContent = dewasa + ' Dewasa, ' + anak + ' Anak';
        
        const total = (dewasa * hargaDewasa) + (anak * hargaAnak);
        document.getElementById('confirmTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        // Show modal
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function confirmBooking() {
        // Submit form
        document.getElementById('formDetail').submit();
    }

    // Close modal when clicking outside
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    function increaseValue(field) {
        const input = document.getElementById(field);
        input.value = parseInt(input.value) + 1;
        calculateTotal();
    }

    function decreaseValue(field) {
        const input = document.getElementById(field);
        const min = field === 'dewasa' ? 1 : 0;
        if (parseInt(input.value) > min) {
            input.value = parseInt(input.value) - 1;
            calculateTotal();
        }
    }

    function calculateTotal() {
        const dewasa = parseInt(document.getElementById('dewasa').value);
        const anak = parseInt(document.getElementById('anak').value);

        const totalDewasa = dewasa * hargaDewasa;
        const totalAnak = anak * hargaAnak;
        const grandTotal = totalDewasa + totalAnak;

        document.getElementById('labelDewasa').textContent = 
            dewasa + ' Dewasa × Rp ' + hargaDewasa.toLocaleString('id-ID');
        document.getElementById('totalDewasa').textContent = 
            'Rp ' + totalDewasa.toLocaleString('id-ID');

        if (anak > 0) {
            document.getElementById('rowAnak').style.display = 'flex';
            document.getElementById('labelAnak').textContent = 
                anak + ' Anak × Rp ' + hargaAnak.toLocaleString('id-ID');
            document.getElementById('totalAnak').textContent = 
                'Rp ' + totalAnak.toLocaleString('id-ID');
        } else {
            document.getElementById('rowAnak').style.display = 'none';
        }

        document.getElementById('grandTotal').textContent = 
            'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // Kalkulasi awal saat page load
    window.onload = function() {
        calculateTotal();
    };
</script>

<?php include 'includes/footer.php'; ?>