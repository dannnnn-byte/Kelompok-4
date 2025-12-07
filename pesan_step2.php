<?php 
session_start();
include 'koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Cek apakah sudah ada data booking di session
if (!isset($_SESSION['booking'])) {
    header("Location: pesan.php");
    exit;
}

$id_paket = $_GET['id_paket'];
$booking = $_SESSION['booking'];
$total_penumpang = $booking['total_penumpang'];

// Query data paket
$query_paket = "SELECT p.*, k.nama_kota 
                FROM paket_wisata p 
                JOIN kota k ON p.id_kota = k.id_kota 
                WHERE p.id_paket = '$id_paket'";
$result_paket = mysqli_query($conn, $query_paket);
$paket = mysqli_fetch_assoc($result_paket);

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_penumpang'])) {
    // Debug: Cek data yang diterima
    error_log("POST Data: " . print_r($_POST, true));
    
    // Validasi data penumpang
    $valid = true;
    foreach ($_POST['penumpang'] as $index => $p) {
        if (empty($p['nama']) || empty($p['email']) || empty($p['telepon']) || 
            empty($p['alamat']) || empty($p['identitas'])) {
            $valid = false;
            $_SESSION['error'] = "Data penumpang $index tidak lengkap!";
            break;
        }
    }
    
    if ($valid) {
        $_SESSION['penumpang'] = $_POST['penumpang'];
        header("Location: pesan_step3.php?id_paket=$id_paket");
        exit;
    } else {
        // Jangan redirect, tampilkan error
        echo "<script>alert('Mohon lengkapi semua data penumpang!');</script>";
    }
}

// Tombol kembali
if (isset($_GET['back'])) {
    header("Location: pesan.php?id_paket=$id_paket");
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
        <div class="progress-step completed">
            <div class="step-circle">
                <i class="bi bi-check-lg"></i>
            </div>
            <span class="step-label">Detail Pemesanan</span>
        </div>
        <div class="progress-line active"></div>
        <div class="progress-step active">
            <div class="step-circle">2</div>
            <span class="step-label">Data Penumpang</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-circle">3</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <!-- STEP 2: DATA PENUMPANG -->
    <div class="booking-grid">
        
        <?php if (isset($_SESSION['error'])): ?>
        <div style="grid-column: 1 / -1; background: #fee; border-left: 4px solid #ef4444; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <strong style="color: #ef4444;">
                <i class="bi bi-exclamation-triangle"></i> Error: 
            </strong>
            <span style="color: #991b1b;"><?= $_SESSION['error'] ?></span>
        </div>
        <?php 
            unset($_SESSION['error']); 
        endif; 
        ?>
        
        <!-- Sidebar Daftar Penumpang -->
        <div class="sidebar-info">
            <div class="passenger-list-card">
                <h3 class="sidebar-title">
                    <i class="bi bi-people"></i>
                    Daftar Penumpang
                </h3>
                <div id="passengerList">
                    <?php for ($i = 1; $i <= $total_penumpang; $i++): 
                        $tipe = $i <= $booking['dewasa'] ? 'Dewasa' : 'Anak';
                        $nama = isset($_SESSION['penumpang'][$i]['nama']) ? $_SESSION['penumpang'][$i]['nama'] : '';
                        $completed = !empty($nama) ? 'completed' : '';
                    ?>
                    <div class="passenger-item <?= $completed ?> <?= $i == 1 ? 'active' : '' ?>" 
                         id="passengerItem<?= $i ?>" onclick="showPassenger(<?= $i ?>)">
                        <div class="passenger-icon">
                            <i class="bi bi-<?= !empty($nama) ? 'check-circle' : 'person' ?>"></i>
                        </div>
                        <div class="passenger-text">
                            <span class="passenger-number">Penumpang <?= $i ?></span>
                            <span class="passenger-type"><?= $tipe ?><?= !empty($nama) ? ' - ' . $nama : '' ?></span>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="progress-bar-container">
                    <p class="progress-text">Progress Pengisian</p>
                    <div class="progress-bar">
                        <?php for ($i = 1; $i <= $total_penumpang; $i++): 
                            $nama = isset($_SESSION['penumpang'][$i]['nama']) ? $_SESSION['penumpang'][$i]['nama'] : '';
                            $filled = !empty($nama) ? 'filled' : '';
                        ?>
                        <div class="progress-segment <?= $filled ?>" id="progressSegment<?= $i ?>"></div>
                        <?php endfor; ?>
                    </div>
                    <p class="progress-count">
                        <span id="filledCount">
                            <?php 
                            $count = 0;
                            if (isset($_SESSION['penumpang'])) {
                                foreach ($_SESSION['penumpang'] as $p) {
                                    if (!empty($p['nama'])) $count++;
                                }
                            }
                            echo $count;
                            ?>
                        </span> dari <?= $total_penumpang ?> selesai
                    </p>
                </div>

                <!-- Info Paket Kecil -->
                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #f3f4f6;">
                    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 5px;">Paket Dipilih</p>
                    <p style="font-size: 0.95rem; font-weight: 600; color: #1f2937; margin: 0;"><?= $paket['nama_paket'] ?></p>
                    <p style="font-size: 0.85rem; color: #6b7280; margin-top: 5px;">
                        <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($booking['tanggal'])) ?>
                    </p>
                    
                    <!-- Debug Button -->
                    <button type="button" onclick="checkAllData()" 
                            style="width: 100%; margin-top: 15px; padding: 10px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 0.85rem; cursor: pointer;">
                        <i class="bi bi-bug"></i> Cek Semua Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Data Penumpang -->
        <div class="form-section">
            <form method="POST" id="formPenumpang">
                <input type="hidden" name="submit_penumpang" value="1">
                
                <?php for ($i = 1; $i <= $total_penumpang; $i++): 
                    $tipe = $i <= $booking['dewasa'] ? 'Dewasa' : 'Anak';
                    // Ambil data dari session jika ada
                    $nama = isset($_SESSION['penumpang'][$i]['nama']) ? $_SESSION['penumpang'][$i]['nama'] : '';
                    $email = isset($_SESSION['penumpang'][$i]['email']) ? $_SESSION['penumpang'][$i]['email'] : '';
                    $telepon = isset($_SESSION['penumpang'][$i]['telepon']) ? $_SESSION['penumpang'][$i]['telepon'] : '';
                    $alamat = isset($_SESSION['penumpang'][$i]['alamat']) ? $_SESSION['penumpang'][$i]['alamat'] : '';
                    $identitas = isset($_SESSION['penumpang'][$i]['identitas']) ? $_SESSION['penumpang'][$i]['identitas'] : '';
                ?>
                <div class="form-card passenger-form" id="passengerForm<?= $i ?>" 
                     style="display: <?= $i == 1 ? 'block' : 'none' ?>">
                    <div class="form-header">
                        <h2 class="form-title">Data Penumpang <?= $i ?></h2>
                        <span class="badge-type"><?= $tipe ?></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="penumpang[<?= $i ?>][nama]" 
                               value="<?= htmlspecialchars($nama) ?>"
                               class="form-control" 
                               placeholder="Masukkan nama sesuai KTP/Paspor" required 
                               onchange="updatePassengerStatus(<?= $i ?>)">
                        <input type="hidden" name="penumpang[<?= $i ?>][tipe]" value="<?= $tipe ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="penumpang[<?= $i ?>][email]" 
                                   value="<?= htmlspecialchars($email) ?>"
                                   class="form-control" 
                                   placeholder="contoh@email.com" 
                                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                   title="Masukkan email yang valid (contoh: nama@domain.com)"
                                   required 
                                   onblur="validateEmail(this)">
                        </div>
                        <small class="form-hint" style="display: none; color: #ef4444; font-size: 0.85rem; margin-top: 5px;">
                            <i class="bi bi-exclamation-circle"></i> Format email tidak valid
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i class="bi bi-phone"></i>
                            <input type="tel" name="penumpang[<?= $i ?>][telepon]" 
                                   value="<?= htmlspecialchars($telepon) ?>"
                                   class="form-control" 
                                   placeholder="08xxxxxxxxxx" 
                                   pattern="^(08|62)[0-9]{8,13}$"
                                   title="Nomor telepon harus diawali 08 atau 62 (8-13 digit)"
                                   minlength="10"
                                   maxlength="15"
                                   required
                                   onblur="validatePhone(this)"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <small class="form-hint" style="display: none; color: #ef4444; font-size: 0.85rem; margin-top: 5px;">
                            <i class="bi bi-exclamation-circle"></i> Nomor telepon tidak valid (gunakan format 08xxx atau 62xxx)
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <i class="bi bi-house"></i>
                            <textarea name="penumpang[<?= $i ?>][alamat]" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Masukkan alamat lengkap" required><?= htmlspecialchars($alamat) ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Identitas (KTP/Paspor) <span class="required">*</span></label>
                        <input type="text" name="penumpang[<?= $i ?>][identitas]" 
                               value="<?= htmlspecialchars($identitas) ?>"
                               class="form-control" 
                               placeholder="Nomor KTP (16 digit) atau Paspor"
                               minlength="6"
                               maxlength="20"
                               required
                               onblur="validateIdentitas(this)"
                               oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase()">
                        <small class="form-hint" style="display: none; color: #ef4444; font-size: 0.85rem; margin-top: 5px;">
                            <i class="bi bi-exclamation-circle"></i> KTP harus 16 digit angka atau format Paspor yang valid
                        </small>
                    </div>

                    <div class="navigation-buttons">
                        <?php if ($i == 1): ?>
                        <a href="pesan.php?id_paket=<?= $id_paket ?>" class="btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Kembali ke Detail
                        </a>
                        <?php else: ?>
                        <button type="button" class="btn-secondary" onclick="previousPassenger(<?= $i ?>)">
                            <i class="bi bi-arrow-left"></i>
                            Sebelumnya
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($i < $total_penumpang): ?>
                        <button type="button" class="btn-primary" onclick="nextPassenger(<?= $i ?>)">
                            Selanjutnya
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <?php else: ?>
                        <button type="submit" class="btn-primary" onclick="return validateAllPassengers()">
                            Lanjut ke Konfirmasi
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </form>
        </div>
    </div>
</div>

<script>
    const totalPenumpang = <?= $total_penumpang ?>;
    let currentPassenger = 1;

    function showPassenger(num) {
        // Sembunyikan semua form
        for (let i = 1; i <= totalPenumpang; i++) {
            document.getElementById('passengerForm' + i).style.display = 'none';
            document.getElementById('passengerItem' + i).classList.remove('active');
        }
        // Tampilkan form yang dipilih
        document.getElementById('passengerForm' + num).style.display = 'block';
        document.getElementById('passengerItem' + num).classList.add('active');
        currentPassenger = num;
    }

    function nextPassenger(current) {
        const form = document.getElementById('passengerForm' + current);
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let allFilled = true;
        let emptyFields = [];

        inputs.forEach(input => {
            if (!input.value.trim()) {
                allFilled = false;
                input.classList.add('error');
                input.style.borderColor = '#ef4444';
                input.style.backgroundColor = '#fee';
                
                // Get field label
                const label = input.closest('.form-group').querySelector('label').textContent.replace(' *', '');
                emptyFields.push(label);
            } else {
                input.classList.remove('error');
                input.style.borderColor = '#e5e7eb';
                input.style.backgroundColor = 'white';
            }
        });

        if (allFilled && current < totalPenumpang) {
            // Mark as completed
            document.getElementById('passengerItem' + current).classList.add('completed');
            document.getElementById('progressSegment' + current).classList.add('filled');
            updateProgress();
            
            showPassenger(current + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else if (!allFilled) {
            alert('⚠️ Mohon lengkapi data berikut:\n\n' + emptyFields.map((f, i) => (i + 1) + '. ' + f).join('\n'));
            
            // Focus ke field pertama yang kosong
            const firstEmpty = form.querySelector('input.error, textarea.error');
            if (firstEmpty) {
                firstEmpty.focus();
                firstEmpty.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    function previousPassenger(current) {
        if (current > 1) {
            showPassenger(current - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function updatePassengerStatus(num) {
        const form = document.getElementById('passengerForm' + num);
        const nama = form.querySelector('input[name*="nama"]').value;
        
        if (nama.trim()) {
            document.getElementById('passengerItem' + num).classList.add('completed');
            document.getElementById('progressSegment' + num).classList.add('filled');
            
            // Update icon
            const icon = document.querySelector('#passengerItem' + num + ' .passenger-icon i');
            icon.className = 'bi bi-check-circle';
            
            // Update nama di sidebar
            const passengerType = document.querySelector('#passengerItem' + num + ' .passenger-type');
            const tipe = passengerType.textContent.split(' - ')[0];
            passengerType.textContent = tipe + ' - ' + nama;
        }
        
        updateProgress();
    }

    function updateProgress() {
        let filled = 0;
        for (let i = 1; i <= totalPenumpang; i++) {
            if (document.getElementById('passengerItem' + i).classList.contains('completed')) {
                filled++;
            }
        }
        document.getElementById('filledCount').textContent = filled;
    }

    // Fungsi untuk cek semua data (debugging)
    function checkAllData() {
        let report = '📋 LAPORAN DATA PENUMPANG\n\n';
        let allComplete = true;
        
        for (let i = 1; i <= totalPenumpang; i++) {
            const form = document.getElementById('passengerForm' + i);
            const nama = form.querySelector('input[name*="nama"]').value.trim();
            const email = form.querySelector('input[name*="email"]').value.trim();
            const telepon = form.querySelector('input[name*="telepon"]').value.trim();
            const alamat = form.querySelector('textarea[name*="alamat"]').value.trim();
            const identitas = form.querySelector('input[name*="identitas"]').value.trim();
            
            report += `Penumpang ${i}:\n`;
            report += `- Nama: ${nama || '❌ KOSONG'}\n`;
            report += `- Email: ${email || '❌ KOSONG'}\n`;
            report += `- Telepon: ${telepon || '❌ KOSONG'}\n`;
            report += `- Alamat: ${alamat ? '✅ Terisi' : '❌ KOSONG'}\n`;
            report += `- Identitas: ${identitas || '❌ KOSONG'}\n`;
            
            if (!nama || !email || !telepon || !alamat || !identitas) {
                report += `Status: ❌ TIDAK LENGKAP\n\n`;
                allComplete = false;
            } else {
                report += `Status: ✅ LENGKAP\n\n`;
            }
        }
        
        report += allComplete ? 
            '✅ SEMUA DATA LENGKAP!\nSiap untuk lanjut ke konfirmasi.' : 
            '⚠️ ADA DATA YANG BELUM LENGKAP!\nMohon lengkapi terlebih dahulu.';
        
        alert(report);
        
        console.log('=== DEBUG DATA ===');
        console.log('Total Penumpang:', totalPenumpang);
        console.log('Current Passenger:', currentPassenger);
        console.log('All Complete:', allComplete);
    }

    // Fungsi validasi semua penumpang
    function validateAllPassengers() {
        let allValid = true;
        let emptyPassenger = 0;
        let emptyFields = [];
        
        for (let i = 1; i <= totalPenumpang; i++) {
            const form = document.getElementById('passengerForm' + i);
            const nama = form.querySelector('input[name*="nama"]').value.trim();
            const email = form.querySelector('input[name*="email"]').value.trim();
            const telepon = form.querySelector('input[name*="telepon"]').value.trim();
            const alamat = form.querySelector('textarea[name*="alamat"]').value.trim();
            const identitas = form.querySelector('input[name*="identitas"]').value.trim();
            
            if (!nama || !email || !telepon || !alamat || !identitas) {
                allValid = false;
                emptyPassenger = i;
                
                if (!nama) emptyFields.push('Nama Lengkap');
                if (!email) emptyFields.push('Email');
                if (!telepon) emptyFields.push('No. Telepon');
                if (!alamat) emptyFields.push('Alamat');
                if (!identitas) emptyFields.push('No. Identitas');
                
                break; // Stop di penumpang pertama yang tidak lengkap
            }
        }
        
        if (!allValid) {
            alert('⚠️ Data Penumpang ' + emptyPassenger + ' belum lengkap!\n\nField yang masih kosong:\n- ' + emptyFields.join('\n- '));
            showPassenger(emptyPassenger);
            
            // Highlight field yang kosong
            const form = document.getElementById('passengerForm' + emptyPassenger);
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#ef4444';
                    input.style.backgroundColor = '#fee';
                } else {
                    input.style.borderColor = '#e5e7eb';
                    input.style.backgroundColor = 'white';
                }
            });
            
            return false;
        }
        
        return confirm('✅ Semua data penumpang sudah lengkap!\n\nApakah Anda yakin ingin melanjutkan ke konfirmasi?');
    }

    // Validasi form sebelum submit
    document.getElementById('formPenumpang').addEventListener('submit', function(e) {
        let allValid = true;
        let emptyPassenger = 0;
        
        for (let i = 1; i <= totalPenumpang; i++) {
            const form = document.getElementById('passengerForm' + i);
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    allValid = false;
                    emptyPassenger = i;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#e5e7eb';
                }
            });
        }
        
        if (!allValid) {
            e.preventDefault();
            alert('Mohon lengkapi data SEMUA penumpang sebelum melanjutkan!\n\nPenumpang ' + emptyPassenger + ' masih ada data yang kosong.');
            showPassenger(emptyPassenger);
            return false;
        }
    });
</script>

<?php include 'includes/footer.php'; ?>