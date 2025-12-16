<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_user = $_SESSION['id_user'] ?? null;
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

// ================= GET NOTIFICATIONS =================
if ($action == 'get_notifications') {
    if ($is_admin) {
        // Admin notifications
        $query = "SELECT 
                    'order' as type,
                    p.id_pemesanan as ref_id,
                    CONCAT('Pemesanan baru: ', pk.nama_paket) as message,
                    p.tanggal_pesan as created_at,
                    p.status_bayar as status
                  FROM pemesanan p
                  JOIN paket_wisata pk ON p.id_paket = pk.id_paket
                  WHERE p.tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  ORDER BY p.tanggal_pesan DESC
                  LIMIT 10";
    } elseif ($id_user) {
        // User notifications
        $query = "SELECT 
                    'booking' as type,
                    id_pemesanan as ref_id,
                    CONCAT('Status booking ', kode_booking, ': ', status_bayar) as message,
                    tanggal_pesan as created_at,
                    status_bayar as status
                  FROM pemesanan
                  WHERE id_user = '$id_user'
                  ORDER BY tanggal_pesan DESC
                  LIMIT 10";
    } else {
        echo json_encode(['success' => false, 'notifications' => []]);
        exit;
    }
    
    $result = mysqli_query($conn, $query);
    $notifications = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    
    echo json_encode(['success' => true, 'notifications' => $notifications]);
}

// ================= GET UNREAD COUNT =================
elseif ($action == 'get_unread_count') {
    if ($is_admin) {
        $count = mysqli_fetch_assoc(mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM pemesanan 
             WHERE tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"))['total'];
    } elseif ($id_user) {
        $count = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(*) as total FROM pemesanan 
             WHERE id_user = '$id_user' AND tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"))['total'];
    } else {
        $count = 0;
    }
    
    echo json_encode(['success' => true, 'count' => $count]);
}

// ================= MARK AS READ =================
elseif ($action == 'mark_read') {
    // In future, implement a read_notifications table
    echo json_encode(['success' => true]);
}
?>
