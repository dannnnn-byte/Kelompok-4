<?php
// Buffer output to prevent stray whitespace from breaking JSON
ob_start();

session_start();
include 'koneksi.php';

// Enable error logging (hide display)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Return JSON even on fatal errors
header('Content-Type: application/json');
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Clean any partial output and return JSON
        if (function_exists('ob_get_level') && ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan di server.',
            'detail' => $error['message']
        ]);
    }
});

error_log("=== Payment Processor Called ===");
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$kode_booking = $_POST['kode_booking'] ?? '';

if (empty($kode_booking)) {
    echo json_encode(['success' => false, 'message' => 'Kode booking tidak ditemukan']);
    exit;
}

// Get pemesanan data
$kode_booking_escaped = mysqli_real_escape_string($conn, $kode_booking);
$query = "SELECT * FROM pemesanan WHERE kode_booking = '$kode_booking_escaped'";
$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("Query error: " . mysqli_error($conn));
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    error_log("Pemesanan tidak ditemukan");
    echo json_encode(['success' => false, 'message' => 'Pemesanan tidak ditemukan']);
    exit;
}

switch ($action) {
    case 'generate':
        $method = $_POST['method'] ?? '';
        if ($method === 'qris') {
            generateQRIS($pemesanan, $conn);
        } elseif ($method === 'va') {
            generateVA($pemesanan, $conn);
        } else {
            echo json_encode(['success' => false, 'message' => 'Metode pembayaran tidak valid']);
        }
        break;

    case 'check_status':
        checkPaymentStatus($kode_booking, $conn);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}

function generateQRIS($pemesanan, $conn) {
    error_log("GenerateQRIS called");
    
    $kode_booking = mysqli_real_escape_string($conn, $pemesanan['kode_booking']);
    
    // Check if QRIS already exists
    $query = "SELECT * FROM pembayaran WHERE kode_booking = '$kode_booking' AND metode_bayar = 'qris'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query error: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }
    
    if (mysqli_num_rows($result) > 0) {
        $payment = mysqli_fetch_assoc($result);
        error_log("Existing QRIS found");
    } else {
        // Generate new QRIS payment
        // Get last id_pembayaran
        $query_max = "SELECT MAX(CAST(id_pembayaran AS UNSIGNED)) as max_id FROM pembayaran WHERE id_pembayaran REGEXP '^[0-9]+$'";
        $result_max = mysqli_query($conn, $query_max);
        $row_max = mysqli_fetch_assoc($result_max);
        $next_id = ($row_max['max_id'] ?? 0) + 1;
        
        $qr_code = generateQRCode($pemesanan['kode_booking'], $pemesanan['total_harga']);
        
        $query = "INSERT INTO pembayaran (
            id_pembayaran,
            id_pemesanan,
            kode_booking,
            metode_bayar,
            jumlah_bayar,
            qr_code,
            status_bayar,
            tanggal_bayar
        ) VALUES (
            '$next_id',
            '{$pemesanan['id_pemesanan']}',
            '$kode_booking',
            'qris',
            {$pemesanan['total_harga']},
            '$qr_code',
            'pending',
            NOW()
        )";
        
        if (!mysqli_query($conn, $query)) {
            error_log("Insert error: " . mysqli_error($conn));
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
            return;
        }
        
        $payment = [
            'id_pembayaran' => $next_id,
            'qr_code' => $qr_code
        ];
        
        error_log("New QRIS created: $next_id");
    }
    
    $html = generateQRISHTML($payment, $pemesanan);
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
}

function generateVA($pemesanan, $conn) {
    error_log("GenerateVA called");
    
    $kode_booking = mysqli_real_escape_string($conn, $pemesanan['kode_booking']);
    
    // Check if VA already exists
    $query = "SELECT * FROM pembayaran WHERE kode_booking = '$kode_booking' AND metode_bayar LIKE 'va_%'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query error: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }
    
    $payment = ['banks' => []];
    
    if (mysqli_num_rows($result) > 0) {
        // Load existing VA
        while ($row = mysqli_fetch_assoc($result)) {
            $bank_key = str_replace('va_', '', $row['metode_bayar']);
            $payment['banks'][$bank_key] = [
                'name' => $row['bank'],
                'va_number' => $row['no_va'],
                'code' => substr($row['no_va'], 0, 3)
            ];
        }
        error_log("Existing VA found");
    } else {
        // Generate new VA
        $banks = [
            'bca' => ['name' => 'BCA', 'code' => '014'],
            'bni' => ['name' => 'BNI', 'code' => '009'],
            'bri' => ['name' => 'BRI', 'code' => '002'],
            'mandiri' => ['name' => 'Mandiri', 'code' => '008']
        ];
        
        // Get last id_pembayaran
        $query_max = "SELECT MAX(CAST(id_pembayaran AS UNSIGNED)) as max_id FROM pembayaran WHERE id_pembayaran REGEXP '^[0-9]+$'";
        $result_max = mysqli_query($conn, $query_max);
        $row_max = mysqli_fetch_assoc($result_max);
        $base_id = ($row_max['max_id'] ?? 0) + 1;
        
        foreach ($banks as $key => $bank) {
            $va_number = $bank['code'] . generateVANumber();
            
            $query = "INSERT INTO pembayaran (
                id_pembayaran,
                id_pemesanan,
                kode_booking,
                metode_bayar,
                no_va,
                bank,
                jumlah_bayar,
                status_bayar,
                tanggal_bayar
            ) VALUES (
                '$base_id',
                '{$pemesanan['id_pemesanan']}',
                '$kode_booking',
                'va_{$key}',
                '$va_number',
                '{$bank['name']}',
                {$pemesanan['total_harga']},
                'pending',
                NOW()
            )";
            
            if (!mysqli_query($conn, $query)) {
                error_log("Insert VA error: " . mysqli_error($conn));
                continue;
            }
            
            $payment['banks'][$key] = [
                'name' => $bank['name'],
                'va_number' => $va_number,
                'code' => $bank['code']
            ];
            
            $base_id++;
        }
        
        error_log("New VA created");
    }
    
    if (empty($payment['banks'])) {
        echo json_encode(['success' => false, 'message' => 'Gagal generate Virtual Account']);
        return;
    }
    
    $html = generateVAHTML($payment, $pemesanan);
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
}

function generateQRCode($kode_booking, $amount) {
    return "QRIS_" . $kode_booking . "_" . $amount;
}

function generateVANumber() {
    // Generate a 10-digit numeric string; fallback if random_int is unavailable
    $digits = '';
    $useRandomInt = function_exists('random_int');
    for ($i = 0; $i < 10; $i++) {
        $d = $useRandomInt ? random_int(0, 9) : mt_rand(0, 9);
        $digits .= (string) $d;
    }
    return $digits;
}

function generateQRISHTML($payment, $pemesanan) {
    $amount = number_format($pemesanan['total_harga'], 0, ',', '.');
    
    $qr_data = urlencode("QRIS|{$payment['qr_code']}|{$pemesanan['total_harga']}");
    $qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$qr_data}";
    
    return "
    <div class='qris-container'>
        <div class='payment-method-header'>
            <h4><i class='bi bi-qr-code'></i> Pembayaran QRIS</h4>
            <span class='badge-active'>Aktif</span>
        </div>
        
        <div class='qris-content'>
            <div class='qr-code-wrapper'>
                <img src='{$qr_image}' alt='QR Code' class='qr-code-image'>
            </div>
            
            <div class='payment-amount-box'>
                <p class='amount-label'>Total Pembayaran</p>
                <h3 class='amount-value'>Rp {$amount}</h3>
            </div>
            
            <div class='payment-instructions'>
                <h5><i class='bi bi-info-circle'></i> Cara Pembayaran:</h5>
                <ol>
                    <li>Buka aplikasi e-wallet Anda (GoPay, OVO, Dana, ShopeePay, dll)</li>
                    <li>Pilih menu <strong>Scan QR Code</strong></li>
                    <li>Arahkan kamera ke QR Code di atas</li>
                    <li>Pastikan jumlah: <strong>Rp {$amount}</strong></li>
                    <li>Konfirmasi pembayaran</li>
                    <li>Simpan bukti pembayaran</li>
                </ol>
            </div>
            
            <div class='payment-note'>
                <i class='bi bi-exclamation-circle'></i>
                <p>QR Code ini hanya berlaku untuk pembayaran ini.</p>
            </div>
        </div>
    </div>
    
    <style>
    .qris-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-top: 20px;
        border: 2px solid #10b981;
    }
    
    .payment-method-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .payment-method-header h4 {
        margin: 0;
        color: #1f2937;
        font-size: 1.2rem;
    }
    
    .badge-active {
        background: #10b981;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .qris-content {
        text-align: center;
    }
    
    .qr-code-wrapper {
        display: inline-block;
        margin: 20px 0;
        padding: 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .qr-code-image {
        width: 300px;
        height: 300px;
        border-radius: 10px;
    }
    
    .payment-amount-box {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        border-left: 4px solid #0284c7;
    }
    
    .amount-label {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0 0 5px 0;
    }
    
    .amount-value {
        color: #0284c7;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    
    .payment-instructions {
        text-align: left;
        background: #f9fafb;
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
    }
    
    .payment-instructions h5 {
        color: #1f2937;
        margin-bottom: 15px;
    }
    
    .payment-instructions ol {
        margin: 0;
        padding-left: 20px;
    }
    
    .payment-instructions li {
        margin-bottom: 10px;
        color: #4b5563;
        line-height: 1.6;
    }
    
    .payment-note {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fef3c7;
        padding: 12px;
        border-radius: 8px;
        margin-top: 15px;
        border-left: 4px solid #f59e0b;
    }
    
    .payment-note i {
        color: #f59e0b;
        font-size: 1.2rem;
    }
    
    .payment-note p {
        margin: 0;
        color: #92400e;
        font-size: 0.9rem;
    }
    </style>
    ";
}

function generateVAHTML($payment, $pemesanan) {
    $amount = number_format($pemesanan['total_harga'], 0, ',', '.');
    
    $html = "
    <div class='va-container'>
        <div class='payment-method-header'>
            <h4><i class='bi bi-bank'></i> Virtual Account</h4>
            <span class='badge-active'>Aktif</span>
        </div>
        
        <div class='va-tabs'>
    ";
    
    $index = 0;
    foreach ($payment['banks'] as $key => $bank) {
        $active = $index === 0 ? 'active' : '';
        $html .= "
            <button class='va-tab {$active}' onclick='showVABank(\"{$key}\")' id='tab-{$key}'>
                <span>{$bank['name']}</span>
            </button>
        ";
        $index++;
    }
    
    $html .= "</div><div class='va-content'>";
    
    $index = 0;
    foreach ($payment['banks'] as $key => $bank) {
        $display = $index === 0 ? 'block' : 'none';
        $html .= "
        <div class='va-detail' id='va-{$key}' style='display: {$display}'>
            <div class='va-number-box'>
                <p class='va-label'>Nomor Virtual Account {$bank['name']}</p>
                <div class='va-number-display'>
                    <h3 class='va-number'>{$bank['va_number']}</h3>
                    <button class='btn-copy' onclick='copyText(\"{$bank['va_number']}\", this)'>
                        <i class='bi bi-copy'></i> Salin
                    </button>
                </div>
            </div>
            
            <div class='payment-amount-box'>
                <p class='amount-label'>Total Pembayaran</p>
                <div class='amount-display'>
                    <h3 class='amount-value'>Rp {$amount}</h3>
                    <button class='btn-copy' onclick='copyText(\"{$pemesanan['total_harga']}\", this)'>
                        <i class='bi bi-copy'></i> Salin
                    </button>
                </div>
            </div>
            
            <div class='payment-instructions'>
                <h5><i class='bi bi-info-circle'></i> Cara Pembayaran via ATM {$bank['name']}:</h5>
                <ol>
                    <li>Masukkan kartu ATM dan PIN</li>
                    <li>Pilih menu <strong>Transfer</strong></li>
                    <li>Pilih <strong>Virtual Account</strong></li>
                    <li>Masukkan nomor: <strong>{$bank['va_number']}</strong></li>
                    <li>Masukkan jumlah: <strong>Rp {$amount}</strong></li>
                    <li>Konfirmasi transaksi</li>
                    <li>Simpan struk pembayaran</li>
                </ol>
            </div>
        </div>
        ";
        $index++;
    }
    
    $html .= "
        </div>
        
        <div class='payment-note'>
            <i class='bi bi-exclamation-circle'></i>
            <p>Transfer harus sesuai nominal. Pembayaran otomatis dikonfirmasi setelah berhasil.</p>
        </div>
    </div>
    
    <script>
    function showVABank(bank) {
        document.querySelectorAll('.va-detail').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.va-tab').forEach(el => el.classList.remove('active'));
        
        document.getElementById('va-' + bank).style.display = 'block';
        document.getElementById('tab-' + bank).classList.add('active');
    }
    </script>
    
    <style>
    .va-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-top: 20px;
        border: 2px solid #3b82f6;
    }
    
    .va-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .va-tab {
        flex: 1;
        min-width: 100px;
        padding: 15px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .va-tab:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }
    
    .va-tab.active {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }
    
    .va-number-box {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        border-left: 4px solid #0284c7;
    }
    
    .va-label {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0 0 10px 0;
    }
    
    .va-number-display, .amount-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }
    
    .va-number {
        color: #0284c7;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }
    
    .btn-copy {
        background: #0284c7;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .btn-copy:hover {
        background: #0369a1;
        transform: translateY(-2px);
    }
    </style>
    ";
    
    return $html;
}

function checkPaymentStatus($kode_booking, $conn) {
    $kode_booking = mysqli_real_escape_string($conn, $kode_booking);
    $query = "SELECT status_bayar FROM pembayaran WHERE kode_booking = '$kode_booking' ORDER BY tanggal_bayar DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $payment = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true,
            'status' => $payment['status_bayar']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Payment not found'
        ]);
    }
}
