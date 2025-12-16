<?php
session_start();

/* ================= PROTEKSI ADMIN ================= */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

/* ================= PROSES DELETE ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pemesanan'])) {
    $id_pemesanan = mysqli_real_escape_string($conn, $_POST['id_pemesanan']);
    
    // Mulai transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data pemesanan untuk logging
        $query_get = "SELECT kode_booking FROM pemesanan WHERE id_pemesanan = '$id_pemesanan'";
        $result_get = mysqli_query($conn, $query_get);
        $booking = mysqli_fetch_assoc($result_get);
        
        if (!$booking) {
            throw new Exception("Pemesanan tidak ditemukan");
        }
        
        $kode_booking = $booking['kode_booking'];
        
        // Hapus data pembayaran terkait (jika ada)
        $query_delete_payment = "DELETE FROM pembayaran WHERE kode_booking = '$kode_booking'";
        if (!mysqli_query($conn, $query_delete_payment)) {
            throw new Exception("Error hapus pembayaran: " . mysqli_error($conn));
        }
        
        // Hapus data penumpang
        $query_delete_penumpang = "DELETE FROM penumpang WHERE id_pemesanan = '$id_pemesanan'";
        if (!mysqli_query($conn, $query_delete_penumpang)) {
            throw new Exception("Error hapus penumpang: " . mysqli_error($conn));
        }
        
        // Hapus data booking_log (jika ada)
        $query_delete_log = "DELETE FROM booking_log WHERE id_pemesanan = '$id_pemesanan'";
        mysqli_query($conn, $query_delete_log);
        
        // Hapus data pemesanan
        $query_delete_pemesanan = "DELETE FROM pemesanan WHERE id_pemesanan = '$id_pemesanan'";
        if (!mysqli_query($conn, $query_delete_pemesanan)) {
            throw new Exception("Error hapus pemesanan: " . mysqli_error($conn));
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['success_message'] = "Pemesanan $kode_booking berhasil dihapus";
        header("Location: dashboard.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
} else {
    // Redirect jika tidak ada POST
    header("Location: dashboard.php");
    exit;
}
?>
