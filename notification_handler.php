<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_user = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
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
        // User notifications - show user's bookings
        $query = "SELECT 
                    'booking' as type,
                    id_pemesanan as ref_id,
                    CONCAT('Status booking ', kode_booking, ': ', status_bayar) as message,
                    tanggal_pesan as created_at,
                    status_bayar as status
                  FROM pemesanan
                  WHERE id_user = " . (int)$id_user . "
                  ORDER BY tanggal_pesan DESC
                  LIMIT 10";
    } else {
        echo json_encode(['success' => false, 'notifications' => []]);
        exit;
    }
    
    $result = mysqli_query($conn, $query);
    $notifications = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Convert datetime to ISO 8601 format for JavaScript
            if (isset($row['created_at'])) {
                $row['created_at'] = date('c', strtotime($row['created_at']));
            }
            $notifications[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'notifications' => $notifications]);
}

// ================= GET UNREAD COUNT =================
elseif ($action == 'get_unread_count') {
    if ($is_admin) {
        $result = mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM pemesanan 
             WHERE tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $row = mysqli_fetch_assoc($result);
        $count = $row['total'] ?? 0;
    } elseif ($id_user) {
        $result = mysqli_query($conn,
            "SELECT COUNT(*) as total FROM pemesanan 
             WHERE id_user = " . (int)$id_user . " AND tanggal_pesan >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $row = mysqli_fetch_assoc($result);
        $count = $row['total'] ?? 0;
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
