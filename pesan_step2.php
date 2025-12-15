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

    function validateEmail(input) {
        const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
        const hint = input.parentElement.parentElement.querySelector('.form-hint');
        
        if (input.value && !emailPattern.test(input.value)) {
            input.style.borderColor = '#ef4444';
            input.style.backgroundColor = '#fee';
            if (hint) hint.style.display = 'block';
            return false;
        } else {
            input.style.borderColor = '#10b981';
            input.style.backgroundColor = '#f0fdf4';
            if (hint) hint.style.display = 'none';
            return true;
        }
    }

    function validatePhone(input) {
        const phonePattern = /^(08|62)[0-9]{8,13}$/;
        const hint = input.parentElement.parentElement.querySelector('.form-hint');
        
        if (input.value && !phonePattern.test(input.value)) {
            input.style.borderColor = '#ef4444';
            input.style.backgroundColor = '#fee';
            if (hint) hint.style.display = 'block';
            return false;
        } else {
            input.style.borderColor = '#10b981';
            input.style.backgroundColor = '#f0fdf4';
            if (hint) hint.style.display = 'none';
            return true;
        }
    }

    function validateIdentitas(input) {
        const value = input.value.trim();
        const hint = input.parentElement.querySelector('.form-hint');
        
        // KTP harus 16 digit angka
        const isKTP = /^[0-9]{16}$/.test(value);
        // Paspor bisa alfanumerik 6-20 karakter
        const isPaspor = /^[A-Z0-9]{6,20}$/.test(value);
        
        if (value && !isKTP && !isPaspor) {
            input.style.borderColor = '#ef4444';
            input.style.backgroundColor = '#fee';
            if (hint) hint.style.display = 'block';
            return false;
        } else {
            input.style.borderColor = '#10b981';
            input.style.backgroundColor = '#f0fdf4';
            if (hint) hint.style.display = 'none';
            return true;
        }
    }

    function validateField(input, fieldName) {
        const value = input.value.trim();
        
        if (!value) {
            return { valid: false, message: `${fieldName} tidak boleh kosong` };
        }

        // Validasi khusus berdasarkan tipe field
        if (input.type === 'email') {
            const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
            if (!emailPattern.test(value)) {
                return { valid: false, message: `${fieldName} tidak valid (contoh: nama@email.com)` };
            }
        }

        if (input.type === 'tel' || input.name.includes('telepon')) {
            const phonePattern = /^(08|62)[0-9]{8,13}$/;
            if (!phonePattern.test(value)) {
                return { valid: false, message: `${fieldName} tidak valid (harus diawali 08 atau 62, 10-15 digit)` };
            }
        }

        if (input.name.includes('identitas')) {
            const isKTP = /^[0-9]{16}$/.test(value);
            const isPaspor = /^[A-Z0-9]{6,20}$/.test(value);
            if (!isKTP && !isPaspor) {
                return { valid: false, message: `${fieldName} tidak valid (KTP: 16 digit angka, Paspor: 6-20 karakter alfanumerik)` };
            }
        }

        if (input.tagName === 'TEXTAREA' && value.length < 10) {
            return { valid: false, message: `${fieldName} terlalu singkat (minimal 10 karakter)` };
        }

        return { valid: true };
    }

    function showErrorNotification(message, passengerNum) {
        // Hapus notifikasi lama jika ada
        const oldNotif = document.querySelector('.error-notification');
        if (oldNotif) oldNotif.remove();

        // Buat notifikasi baru
        const notification = document.createElement('div');
        notification.className = 'error-notification';
        notification.innerHTML = `
            <div class="error-content">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div class="error-text">
                    <strong>Peringatan!</strong>
                    <p>${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="error-close">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        // Masukkan ke form yang aktif
        const form = document.getElementById('passengerForm' + passengerNum);
        form.insertBefore(notification, form.firstChild);

        // Auto close setelah 5 detik
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    function nextPassenger(current) {
        const form = document.getElementById('passengerForm' + current);
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let errors = [];

        inputs.forEach(input => {
            const label = input.closest('.form-group').querySelector('label').textContent.replace(' *', '');
            const validation = validateField(input, label);
            
            if (!validation.valid) {
                errors.push(validation.message);
                input.style.borderColor = '#ef4444';
                input.style.backgroundColor = '#fee';
            } else {
                input.style.borderColor = '#10b981';
                input.style.backgroundColor = '#f0fdf4';
            }
        });

        if (errors.length > 0) {
            const errorMessage = `<strong>Data Penumpang ${current} belum lengkap:</strong><ul style="margin: 10px 0 0 20px; text-align: left;">` +
                errors.map(err => `<li>${err}</li>`).join('') +
                '</ul>';
            
            showErrorNotification(errorMessage, current);
            
            // Focus ke field pertama yang error
            const firstError = form.querySelector('input[style*="border-color: rgb(239, 68, 68)"], textarea[style*="border-color: rgb(239, 68, 68)"]');
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        // Jika valid, lanjut ke penumpang berikutnya
        if (current < totalPenumpang) {
            document.getElementById('passengerItem' + current).classList.add('completed');
            document.getElementById('progressSegment' + current).classList.add('filled');
            updateProgress();
            
            showPassenger(current + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
            
            const icon = document.querySelector('#passengerItem' + num + ' .passenger-icon i');
            icon.className = 'bi bi-check-circle';
            
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

    function checkAllData() {
        let report = '📋 LAPORAN DATA PENUMPANG\n\n';
        let allComplete = true;
        let incompletePassengers = [];
        
        for (let i = 1; i <= totalPenumpang; i++) {
            const form = document.getElementById('passengerForm' + i);
            const nama = form.querySelector('input[name*="nama"]').value.trim();
            const email = form.querySelector('input[name*="email"]').value.trim();
            const telepon = form.querySelector('input[name*="telepon"]').value.trim();
            const alamat = form.querySelector('textarea[name*="alamat"]').value.trim();
            const identitas = form.querySelector('input[name*="identitas"]').value.trim();
            
            let passengerErrors = [];
            
            if (!nama) passengerErrors.push('Nama');
            if (!email) passengerErrors.push('Email');
            else if (!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i.test(email)) passengerErrors.push('Email (format salah)');
            
            if (!telepon) passengerErrors.push('Telepon');
            else if (!/^(08|62)[0-9]{8,13}$/.test(telepon)) passengerErrors.push('Telepon (format salah)');
            
            if (!alamat) passengerErrors.push('Alamat');
            if (!identitas) passengerErrors.push('Identitas');
            
            report += `Penumpang ${i}:\n`;
            if (passengerErrors.length === 0) {
                report += `✅ LENGKAP - ${nama}\n\n`;
            } else {
                report += `❌ TIDAK LENGKAP\n`;
                report += `   Field bermasalah: ${passengerErrors.join(', ')}\n\n`;
                allComplete = false;
                incompletePassengers.push(i);
            }
        }
        
        report += '\n' + (allComplete ? 
            '✅ SEMUA DATA LENGKAP!\nSiap untuk lanjut ke konfirmasi.' : 
            `⚠️ ${incompletePassengers.length} PENUMPANG BELUM LENGKAP!\nPenumpang: ${incompletePassengers.join(', ')}`);
        
        alert(report);
    }

    function validateAllPassengers() {
        let allValid = true;
        let incompletePassengers = [];
        let allErrors = {};
        
        for (let i = 1; i <= totalPenumpang; i++) {
            const form = document.getElementById('passengerForm' + i);
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let passengerErrors = [];
            
            inputs.forEach(input => {
                const label = input.closest('.form-group').querySelector('label').textContent.replace(' *', '');
                const validation = validateField(input, label);
                
                if (!validation.valid) {
                    passengerErrors.push(validation.message);
                }
            });
            
            if (passengerErrors.length > 0) {
                allValid = false;
                incompletePassengers.push(i);
                allErrors[i] = passengerErrors;
            }
        }
        
        if (!allValid) {
            let errorHTML = `
                <div style="max-height: 400px; overflow-y: auto;">
                    <p style="font-size: 1.1rem; margin-bottom: 15px; color: #dc2626;">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        <strong>${incompletePassengers.length} Penumpang belum lengkap!</strong>
                    </p>
            `;
            
            for (let passengerNum of incompletePassengers) {
                errorHTML += `
                    <div style="background: #fee; padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #ef4444;">
                        <strong style="color: #991b1b;">Penumpang ${passengerNum}:</strong>
                        <ul style="margin: 8px 0 0 20px; color: #7f1d1d;">
                            ${allErrors[passengerNum].map(err => `<li>${err}</li>`).join('')}
                        </ul>
                        <button onclick="showPassenger(${passengerNum}); document.getElementById('validationModal').style.display='none';" 
                                style="margin-top: 8px; padding: 5px 12px; background: #dc2626; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.85rem;">
                            <i class="bi bi-pencil"></i> Perbaiki Sekarang
                        </button>
                    </div>
                `;
            }
            
            errorHTML += '</div>';
            
            showValidationModal(errorHTML);
            return false;
        }
        
        return true;
    }

    function showValidationModal(content) {
        const modal = document.createElement('div');
        modal.id = 'validationModal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s;
        `;
        
        modal.innerHTML = `
            <div style="background: white; border-radius: 20px; padding: 30px; max-width: 600px; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); animation: slideUp 0.3s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f3f4f6;">
                    <h3 style="margin: 0; color: #1f2937;">
                        <i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i>
                        Data Belum Lengkap
                    </h3>
                    <button onclick="document.getElementById('validationModal').remove()" 
                            style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                ${content}
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Validasi form sebelum submit
    document.getElementById('formPenumpang').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateAllPassengers()) {
            return false;
        }
        
        // Jika semua valid, tampilkan konfirmasi
        if (confirm('✅ Semua data penumpang sudah lengkap!\n\nApakah Anda yakin ingin melanjutkan ke konfirmasi?')) {
            this.submit();
        }
    });

    // Real-time validation saat input
    document.addEventListener('DOMContentLoaded', function() {
        // Email validation
        document.querySelectorAll('input[type="email"]').forEach(input => {
            input.addEventListener('blur', function() {
                validateEmail(this);
            });
        });

        // Phone validation
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('blur', function() {
                validatePhone(this);
            });
        });

        // Identitas validation
        document.querySelectorAll('input[name*="identitas"]').forEach(input => {
            input.addEventListener('blur', function() {
                validateIdentitas(this);
            });
        });

        // Hapus error styling saat mulai mengetik
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.style.borderColor = '#e5e7eb';
                    this.style.backgroundColor = 'white';
                }
            });
        });
    });
</script>

<style>
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

@keyframes slideOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

.error-notification {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-left: 4px solid #ef4444;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.error-content > i {
    font-size: 1.5rem;
    color: #dc2626;
    margin-top: 2px;
    flex-shrink: 0;
}

.error-text {
    flex: 1;
}

.error-text strong {
    display: block;
    color: #991b1b;
    font-size: 1.05rem;
    margin-bottom: 5px;
}

.error-text p {
    color: #7f1d1d;
    margin: 0;
    line-height: 1.5;
}

.error-text ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
    color: #7f1d1d;
}

.error-text ul li {
    margin-bottom: 5px;
}

.error-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #991b1b;
    font-size: 1.3rem;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
    flex-shrink: 0;
}

.error-close:hover {
    background: #fca5a5;
    color: #7f1d1d;
}
</style>