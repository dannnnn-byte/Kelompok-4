<?php
session_start();
require_once('tcpdf/tcpdf.php');
include 'koneksi.php';

$kode_booking = $_GET['kode'] ?? '';

if (empty($kode_booking)) {
    die('Kode booking tidak ditemukan');
}

// Get complete booking data
$query = "SELECT p.*, pk.nama_paket, pk.gambar_paket, pk.durasi, k.nama_kota, pk.harga_per_pax,
          pay.metode_bayar, pay.tanggal_bayar, pay.status_bayar
          FROM pemesanan p
          JOIN paket_wisata pk ON p.id_paket = pk.id_paket
          JOIN kota k ON pk.id_kota = k.id_kota
          LEFT JOIN pembayaran pay ON p.kode_booking = pay.kode_booking
          WHERE p.kode_booking = '$kode_booking'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die('Pemesanan tidak ditemukan');
}

$booking = mysqli_fetch_assoc($result);

// Get passenger data
$query_passengers = "SELECT * FROM penumpang WHERE id_pemesanan = '{$booking['id_pemesanan']}'";
$result_passengers = mysqli_query($conn, $query_passengers);
$passengers = [];
while ($row = mysqli_fetch_assoc($result_passengers)) {
    $passengers[] = $row;
}

// Create new PDF document
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo
        $image_file = 'assets/img/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(20, 92, 67);
        $this->Cell(0, 15, 'BUKTI BOOKING', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create PDF instance
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Booking Wisata');
$pdf->SetTitle('Bukti Booking - ' . $kode_booking);
$pdf->SetSubject('Bukti Booking Wisata');

// Set margins
$pdf->SetMargins(15, 40, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Kode Booking Box
$pdf->SetFillColor(240, 253, 244);
$pdf->SetDrawColor(16, 185, 129);
$pdf->SetLineWidth(0.5);
$pdf->Rect(15, $pdf->GetY(), 180, 15, 'DF');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(20, 92, 67);
$pdf->Cell(90, 7, 'KODE BOOKING', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(107, 114, 128);
$pdf->Cell(90, 7, 'Tanggal Booking: ' . date('d M Y', strtotime($booking['tanggal_pesan'])), 0, 1, 'R');

$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(5, 150, 105);
$pdf->Cell(0, 8, $kode_booking, 0, 1, 'L');

$pdf->Ln(5);

// Status Badge
$status_text = [
    'pending' => ['text' => 'MENUNGGU PEMBAYARAN', 'color' => [245, 158, 11]],
    'menunggu_verifikasi' => ['text' => 'MENUNGGU VERIFIKASI', 'color' => [251, 191, 36]],
    'lunas' => ['text' => 'PEMBAYARAN LUNAS', 'color' => [16, 185, 129]],
    'dibatalkan' => ['text' => 'DIBATALKAN', 'color' => [239, 68, 68]]
];

$status = $status_text[$booking['status_bayar']] ?? ['text' => 'UNKNOWN', 'color' => [156, 163, 175]];

$pdf->SetFillColor($status['color'][0], $status['color'][1], $status['color'][2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 8, $status['text'], 0, 1, 'C', true);

$pdf->Ln(5);

// Detail Paket Section
$pdf->SetFillColor(249, 250, 251);
$pdf->SetDrawColor(229, 231, 235);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(31, 41, 55);
$pdf->Cell(0, 8, 'DETAIL PAKET WISATA', 0, 1, 'L', true);

$pdf->Ln(2);

$details = [
    ['Nama Paket', $booking['nama_paket']],
    ['Lokasi', $booking['nama_kota']],
    ['Durasi', $booking['durasi']],
    ['Tanggal Keberangkatan', date('d F Y', strtotime($booking['tgl_tour']))],
    ['Jumlah Peserta', $booking['jumlah_peserta'] . ' Orang (' . $booking['jumlah_dewasa'] . ' Dewasa, ' . $booking['jumlah_anak'] . ' Anak)']
];

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(75, 85, 99);

foreach ($details as $detail) {
    $pdf->Cell(70, 7, $detail[0], 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(31, 41, 55);
    $pdf->Cell(0, 7, ': ' . $detail[1], 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(75, 85, 99);
}

$pdf->Ln(5);

// Daftar Penumpang Section
$pdf->SetFillColor(249, 250, 251);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(31, 41, 55);
$pdf->Cell(0, 8, 'DAFTAR PENUMPANG', 0, 1, 'L', true);

$pdf->Ln(2);

// Table Header
$pdf->SetFillColor(20, 92, 67);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Nama Lengkap', 1, 0, 'L', true);
$pdf->Cell(40, 8, 'Email', 1, 0, 'L', true);
$pdf->Cell(35, 8, 'No. Telepon', 1, 0, 'L', true);
$pdf->Cell(25, 8, 'Identitas', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Tipe', 1, 1, 'C', true);

// Table Content
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(31, 41, 55);
$fill = false;

foreach ($passengers as $index => $passenger) {
    $pdf->SetFillColor(249, 250, 251);
    $pdf->Cell(10, 7, ($index + 1), 1, 0, 'C', $fill);
    $pdf->Cell(50, 7, substr($passenger['nama_lengkap'], 0, 30), 1, 0, 'L', $fill);
    $pdf->Cell(40, 7, substr($passenger['email'], 0, 25), 1, 0, 'L', $fill);
    $pdf->Cell(35, 7, $passenger['no_telepon'], 1, 0, 'L', $fill);
    $pdf->Cell(25, 7, substr($passenger['no_identitas'], 0, 15), 1, 0, 'C', $fill);
    $pdf->Cell(20, 7, $passenger['tipe_penumpang'], 1, 1, 'C', $fill);
    $fill = !$fill;
}

$pdf->Ln(5);

// Rincian Pembayaran Section
$pdf->SetFillColor(249, 250, 251);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(31, 41, 55);
$pdf->Cell(0, 8, 'RINCIAN PEMBAYARAN', 0, 1, 'L', true);

$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(75, 85, 99);

// Price breakdown
$harga_dewasa = $booking['jumlah_dewasa'] * $booking['harga_per_pax'];
$harga_anak = $booking['jumlah_anak'] * ($booking['harga_per_pax'] * 0.7);

$pdf->Cell(130, 7, 'Dewasa (' . $booking['jumlah_dewasa'] . ' x Rp ' . number_format($booking['harga_per_pax'], 0, ',', '.') . ')', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Rp ' . number_format($harga_dewasa, 0, ',', '.'), 0, 1, 'R');

if ($booking['jumlah_anak'] > 0) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(130, 7, 'Anak (' . $booking['jumlah_anak'] . ' x Rp ' . number_format($booking['harga_per_pax'] * 0.7, 0, ',', '.') . ')', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 7, 'Rp ' . number_format($harga_anak, 0, ',', '.'), 0, 1, 'R');
}

$pdf->Ln(2);
$pdf->SetDrawColor(229, 231, 235);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(3);

// Total
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(20, 92, 67);
$pdf->Cell(130, 10, 'TOTAL PEMBAYARAN', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Rp ' . number_format($booking['total_harga'], 0, ',', '.'), 0, 1, 'R');

$pdf->Ln(5);

// Payment Method
if ($booking['metode_bayar']) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(75, 85, 99);
    $pdf->Cell(0, 7, 'Metode Pembayaran: ' . strtoupper($booking['metode_bayar']), 0, 1, 'L');
}

$pdf->Ln(5);

// Informasi Penting
$pdf->SetFillColor(254, 243, 199);
$pdf->SetDrawColor(245, 158, 11);
$pdf->SetLineWidth(0.5);
$pdf->Rect(15, $pdf->GetY(), 180, 40, 'D');

$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(146, 64, 14);
$pdf->Cell(0, 7, 'INFORMASI PENTING', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(120, 53, 15);
$pdf->MultiCell(0, 5, 
    "• Harap membawa dokumen identitas asli (KTP/Paspor) saat keberangkatan\n" .
    "• Datang 30 menit sebelum waktu keberangkatan\n" .
    "• Simpan bukti booking ini sebagai tanda bukti pembayaran\n" .
    "• Untuk perubahan atau pembatalan, hubungi customer service minimal H-3\n" .
    "• E-ticket resmi akan dikirim via email setelah pembayaran terverifikasi",
    0, 'L', false);

$pdf->Ln(5);

// Footer Info
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->MultiCell(0, 5, 
    "Dokumen ini dibuat secara otomatis pada " . date('d F Y H:i') . "\n" .
    "Untuk informasi lebih lanjut, hubungi Customer Service: 0812-3456-7890 atau email: support@wisata.com",
    0, 'C', false);

// Output PDF
$filename = 'Bukti_Booking_' . $kode_booking . '.pdf';
$pdf->Output($filename, 'D'); // D = Download, I = Inline view
?>