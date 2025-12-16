<?php 
session_start();
include 'koneksi.php';

$kode_booking = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';

if (empty($kode_booking)) {
    header("Location: index.php");
    exit;
}

// Ambil data pemesanan lengkap
$query = "SELECT p.*, pk.nama_paket, pk.gambar_paket, pk.durasi, k.nama_kota,
          pb.status_bayar, pb.tanggal_konfirmasi
          FROM pemesanan p
          JOIN paket_wisata pk ON p.id_paket = pk.id_paket
          JOIN kota k ON pk.id_kota = k.id_kota
          LEFT JOIN pembayaran pb ON p.kode_booking = pb.kode_booking
          WHERE p.kode_booking = '$kode_booking'
          LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Tiket tidak ditemukan!");
}

$pemesanan = mysqli_fetch_assoc($result);

// Ambil data penumpang
$query_penumpang = "SELECT * FROM penumpang WHERE id_pemesanan = '{$pemesanan['id_pemesanan']}'";
$result_penumpang = mysqli_query($conn, $query_penumpang);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - <?= $kode_booking ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .page-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .action-button-top {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .btn-back {
            background: #f3f4f6;
            color: #4b5563;
            padding: 18px 40px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #e5e7eb;
            border-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-back i {
            font-size: 1.2rem;
        }

        .btn-download {
            background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
            color: white;
            padding: 18px 45px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 25px rgba(20, 92, 67, 0.4);
            transition: all 0.3s;
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(20, 92, 67, 0.5);
        }

        .btn-download i {
            font-size: 1.3rem;
        }

        /* TICKET DESIGN */
        .ticket-wrapper {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        /* Notch effect di kedua sisi */
        .ticket-wrapper::before,
        .ticket-wrapper::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background: #e0f2fe;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .ticket-wrapper::before {
            left: -15px;
        }

        .ticket-wrapper::after {
            right: -15px;
        }

        /* Header Ticket */
        .ticket-header {
            background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .ticket-header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .ticket-header p {
            opacity: 0.95;
            font-size: 1.15rem;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }

        /* Dashed Line Separator */
        .dashed-line {
            border-bottom: 3px dashed #e5e7eb;
            margin: 35px 0;
            position: relative;
        }

        .dashed-line::before,
        .dashed-line::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #e0f2fe;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .dashed-line::before {
            left: -10px;
        }

        .dashed-line::after {
            right: -10px;
        }

        /* Ticket Body */
        .ticket-body {
            padding: 45px 50px;
        }

        .ticket-section {
            margin-bottom: 40px;
        }

        .section-title {
            color: #145C43;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #d1fae5;
        }

        .section-title i {
            font-size: 1.6rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #10b981;
        }

        .info-label {
            color: #6b7280;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .info-value {
            color: #1f2937;
            font-weight: 700;
            font-size: 1.2rem;
        }

        /* Passenger List */
        .passenger-list {
            background: #f0fdf4;
            padding: 30px;
            border-radius: 15px;
            border: 2px solid #d1fae5;
        }

        .passenger-item {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .passenger-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .passenger-item:last-child {
            margin-bottom: 0;
        }

        .passenger-number {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #145C43 0%, #10b981 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .passenger-info {
            flex: 1;
        }

        .passenger-name {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 1.05rem;
        }

        .passenger-contact {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .passenger-type {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 40px;
            border-radius: 15px;
            border: 3px solid #145C43;
        }

        .qr-code {
            width: 240px;
            height: 240px;
            margin: 30px auto;
            padding: 18px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .qr-text {
            color: #145C43;
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            margin-top: 15px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .status-lunas {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        /* Footer */
        .ticket-footer {
            background: #f9fafb;
            padding: 30px 50px;
            border-top: 2px dashed #e5e7eb;
        }

        .footer-notes {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.9;
        }

        .footer-notes strong {
            color: #145C43;
            display: block;
            margin-bottom: 12px;
            font-size: 1.05rem;
        }

        .footer-notes p {
            margin: 8px 0;
        }

        .print-time {
            text-align: center;
            color: #9ca3af;
            font-size: 0.88rem;
            font-style: italic;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .ticket-body {
                padding: 30px 25px;
            }

            .ticket-footer {
                padding: 25px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .ticket-header h1 {
                font-size: 2rem;
            }

            .action-button-top {
                flex-direction: column;
            }

            .btn-back,
            .btn-download {
                width: 100%;
                justify-content: center;
                padding: 15px 35px;
                font-size: 1rem;
            }

            .ticket-wrapper::before,
            .ticket-wrapper::after {
                display: none;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .action-button-top {
                display: none !important;
            }

            .ticket-wrapper {
                box-shadow: none;
                page-break-inside: avoid;
            }

            .ticket-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Action Buttons -->
        <div class="action-button-top">
            <a href="payment_success.php?kode=<?= $kode_booking ?>" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
            <button onclick="downloadTicket()" class="btn-download">
                <i class="bi bi-download"></i>
                Download Tiket
            </button>
        </div>

        <!-- Ticket -->
        <div class="ticket-wrapper" id="ticketContent">
            <!-- Header -->
            <div class="ticket-header">
                <h1><i class="bi bi-ticket-perforated-fill"></i> E-TICKET</h1>
                <p>Tiket Resmi JawaTrip Indonesia</p>
            </div>

            <!-- Body -->
            <div class="ticket-body">
                <!-- Informasi Booking -->
                <div class="ticket-section">
                    <h3 class="section-title">
                        <i class="bi bi-info-circle-fill"></i>
                        Informasi Booking
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Kode Booking</div>
                            <div class="info-value"><?= $kode_booking ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status Pembayaran</div>
                            <?php 
                            $status = strtolower($pemesanan['status_bayar']);
                            $badge_class = $status == 'lunas' ? 'status-lunas' : 'status-pending';
                            $status_text = $status == 'lunas' ? 'Lunas' : 'Menunggu Verifikasi';
                            $icon = $status == 'lunas' ? 'check-circle-fill' : 'clock-fill';
                            ?>
                            <span class="status-badge <?= $badge_class ?>">
                                <i class="bi bi-<?= $icon ?>"></i>
                                <?= $status_text ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Pemesanan</div>
                            <div class="info-value"><?= date('d M Y, H:i', strtotime($pemesanan['tanggal_pesan'])) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total Pembayaran</div>
                            <div class="info-value" style="color: #145C43;">Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>

                <div class="dashed-line"></div>

                <!-- Paket Wisata -->
                <div class="ticket-section">
                    <h3 class="section-title">
                        <i class="bi bi-geo-alt-fill"></i>
                        Detail Paket Wisata
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nama Paket</div>
                            <div class="info-value"><?= $pemesanan['nama_paket'] ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Destinasi</div>
                            <div class="info-value"><?= $pemesanan['nama_kota'] ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Keberangkatan</div>
                            <div class="info-value"><?= date('d F Y', strtotime($pemesanan['tgl_tour'])) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Durasi</div>
                            <div class="info-value"><?= $pemesanan['durasi'] ?></div>
                        </div>
                    </div>
                </div>

                <div class="dashed-line"></div>

                <!-- Daftar Penumpang -->
                <div class="ticket-section">
                    <h3 class="section-title">
                        <i class="bi bi-people-fill"></i>
                        Daftar Penumpang (<?= $pemesanan['jumlah_peserta'] ?> Orang)
                    </h3>
                    <div class="passenger-list">
                        <?php 
                        $no = 1;
                        while ($p = mysqli_fetch_assoc($result_penumpang)): 
                        ?>
                        <div class="passenger-item">
                            <div class="passenger-number"><?= $no ?></div>
                            <div class="passenger-info">
                                <div class="passenger-name"><?= htmlspecialchars($p['nama_lengkap']) ?></div>
                                <div class="passenger-contact">
                                    <?= htmlspecialchars($p['email']) ?> • <?= htmlspecialchars($p['no_telepon']) ?>
                                </div>
                            </div>
                            <span class="passenger-type">
                                <?= $p['tipe_penumpang'] ?>
                            </span>
                        </div>
                        <?php 
                        $no++;
                        endwhile; 
                        ?>
                    </div>
                </div>

                <div class="dashed-line"></div>

                <!-- QR Code -->
                <div class="ticket-section">
                    <div class="qr-section">
                        <h3 class="section-title" style="justify-content: center; border: none;">
                            <i class="bi bi-qr-code"></i>
                            Kode Verifikasi Tiket
                        </h3>
                        <p style="color: #6b7280; margin-bottom: 5px; font-size: 0.95rem;">Tunjukkan QR Code ini saat check-in</p>
                        <div class="qr-code">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode("JAWATRIP|{$kode_booking}|{$pemesanan['total_harga']}") ?>" 
                                 alt="QR Code">
                        </div>
                        <div class="qr-text"><?= $kode_booking ?></div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="footer-notes">
                    <strong>📋 Catatan Penting:</strong>
                    <p>• Harap tiba 30 menit sebelum waktu keberangkatan</p>
                    <p>• Bawa identitas diri (KTP/Paspor) sesuai data pemesanan</p>
                    <p>• Tiket ini sah dan dilindungi oleh sistem JawaTrip</p>
                </div>
                <div class="print-time">
                    Dicetak pada: <?= date('d F Y, H:i') ?> WIB
                </div>
            </div>
        </div>
    </div>

    <script>
    function downloadTicket() {
        const element = document.getElementById('ticketContent');
        const options = {
            margin: 5,
            filename: 'Tiket-<?= $kode_booking ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Generate PDF
        html2pdf().set(options).from(element).save().then(() => {
            // Setelah PDF didownload, trigger print
            setTimeout(() => {
                window.print();
            }, 500);
        });
    }
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>