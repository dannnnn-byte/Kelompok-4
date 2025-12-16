<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
// Gunakan session key yang benar: app menyimpan di `user_id` untuk user biasa
$id_user = $_SESSION['user_id'] ?? ($_SESSION['id_user'] ?? null);

if (!$id_user) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

// ================= ADD TO WISHLIST =================
if ($action == 'toggle_wishlist') {
    $id_paket = isset($_POST['id_paket']) ? (int)$_POST['id_paket'] : 0;
    
    // Check if already in wishlist
    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE id_user = '$id_user' AND id_paket = '$id_paket'");
    
    if (mysqli_num_rows($check) > 0) {
        // Remove from wishlist
        mysqli_query($conn, "DELETE FROM wishlist WHERE id_user = '$id_user' AND id_paket = '$id_paket'");
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Dihapus dari favorit']);
    } else {
        // Add to wishlist
        mysqli_query($conn, "INSERT INTO wishlist (id_user, id_paket, created_at) VALUES ('$id_user', '$id_paket', NOW())");
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Ditambahkan ke favorit']);
    }
}

// ================= CHECK WISHLIST =================
elseif ($action == 'check_wishlist') {
    $id_paket = isset($_POST['id_paket']) ? (int)$_POST['id_paket'] : 0;
    
    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE id_user = '$id_user' AND id_paket = '$id_paket'");
    echo json_encode(['success' => true, 'in_wishlist' => mysqli_num_rows($check) > 0]);
}

// ================= GET WISHLIST =================
elseif ($action == 'get_wishlist') {
    $query = "SELECT w.*, p.nama_paket, p.harga_per_pax, p.durasi, p.gambar_paket, k.nama_kota
              FROM wishlist w
              JOIN paket_wisata p ON w.id_paket = p.id_paket
              JOIN kota k ON p.id_kota = k.id_kota
              WHERE w.id_user = '$id_user'
              ORDER BY w.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $wishlist = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $wishlist[] = $row;
    }
    
    echo json_encode(['success' => true, 'wishlist' => $wishlist]);
}

// ================= GET WISHLIST COUNT =================
elseif ($action == 'get_count') {
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE id_user = '$id_user'"))['total'];
    echo json_encode(['success' => true, 'count' => $count]);
}
?>
