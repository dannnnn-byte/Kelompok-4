<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ================= SUBMIT REVIEW =================
if ($action == 'submit_review') {
    $id_paket = isset($_POST['id_paket']) ? (int)$_POST['id_paket'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review = isset($_POST['review']) ? mysqli_real_escape_string($conn, trim($_POST['review'])) : '';
    
    // Try both user_id and id_user session keys
    $id_user = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;

    if (!$id_user) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating harus antara 1-5']);
        exit;
    }
    
    if (empty($review)) {
        echo json_encode(['success' => false, 'message' => 'Review tidak boleh kosong']);
        exit;
    }
    
    if ($id_paket < 1) {
        echo json_encode(['success' => false, 'message' => 'ID paket tidak valid']);
        exit;
    }

    // Check if user already reviewed this package
    $check = mysqli_query($conn, "SELECT id FROM paket_reviews WHERE id_paket = '$id_paket' AND id_user = '$id_user'");
    
    if (mysqli_num_rows($check) > 0) {
        // Update existing review
        $query = "UPDATE paket_reviews SET rating = '$rating', review_text = '$review', created_at = NOW() 
                  WHERE id_paket = '$id_paket' AND id_user = '$id_user'";
    } else {
        // Insert new review
        $query = "INSERT INTO paket_reviews (id_paket, id_user, rating, review_text, created_at) 
                  VALUES ('$id_paket', '$id_user', '$rating', '$review', NOW())";
    }

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Review berhasil disimpan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan review: ' . mysqli_error($conn)]);
    }
}

// ================= GET REVIEWS =================
elseif ($action == 'get_reviews') {
    $id_paket = $_POST['id_paket'] ?? 0;
    
    $query = "SELECT r.*, u.nama_lengkap, u.email 
              FROM paket_reviews r 
              LEFT JOIN users u ON r.id_user = u.id_user 
              WHERE r.id_paket = '$id_paket' 
              ORDER BY r.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $reviews = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
    
    // Get average rating
    $avg_query = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total 
                                      FROM paket_reviews WHERE id_paket = '$id_paket'");
    $avg_data = mysqli_fetch_assoc($avg_query);
    
    echo json_encode([
        'success' => true, 
        'reviews' => $reviews,
        'avg_rating' => round($avg_data['avg_rating'], 1),
        'total_reviews' => $avg_data['total']
    ]);
}

// ================= GET STATS =================
elseif ($action == 'get_stats') {
    $id_paket = $_POST['id_paket'] ?? 0;
    
    $stats = [];
    for ($i = 5; $i >= 1; $i--) {
        $q = mysqli_query($conn, "SELECT COUNT(*) as count FROM paket_reviews WHERE id_paket = '$id_paket' AND rating = $i");
        $stats[$i] = mysqli_fetch_assoc($q)['count'];
    }
    
    echo json_encode(['success' => true, 'stats' => $stats]);
}

// ================= EDIT REVIEW =================
elseif ($action == 'edit_review') {
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = isset($_POST['review']) ? mysqli_real_escape_string($conn, trim($_POST['review'])) : '';
    
    $id_user = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
    
    if (!$id_user) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating harus antara 1-5']);
        exit;
    }
    
    if (empty($review_text)) {
        echo json_encode(['success' => false, 'message' => 'Review tidak boleh kosong']);
        exit;
    }
    
    // Verify ownership
    $check = mysqli_query($conn, "SELECT id FROM paket_reviews WHERE id = '$review_id' AND id_user = '$id_user'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengedit review ini']);
        exit;
    }
    
    $query = "UPDATE paket_reviews SET rating = '$rating', review_text = '$review_text' 
              WHERE id = '$review_id' AND id_user = '$id_user'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Review berhasil diupdate!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate review']);
    }
}

// ================= DELETE REVIEW =================
elseif ($action == 'delete_review') {
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    $id_user = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
    
    if (!$id_user) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }
    
    // Verify ownership
    $check = mysqli_query($conn, "SELECT id FROM paket_reviews WHERE id = '$review_id' AND id_user = '$id_user'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menghapus review ini']);
        exit;
    }
    
    $query = "DELETE FROM paket_reviews WHERE id = '$review_id' AND id_user = '$id_user'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Review berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus review']);
    }
}

// ================= SUBMIT ADMIN REPLY =================
elseif ($action == 'submit_reply') {
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    $reply_text = isset($_POST['reply']) ? mysqli_real_escape_string($conn, trim($_POST['reply'])) : '';
    
    // Check if user is admin
    $is_admin = ($_SESSION['role'] ?? '') === 'admin';
    $admin_id = $_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? null;
    
    if (!$is_admin || !$admin_id) {
        echo json_encode(['success' => false, 'message' => 'Hanya admin yang bisa membalas review']);
        exit;
    }
    
    if (empty($reply_text)) {
        echo json_encode(['success' => false, 'message' => 'Reply tidak boleh kosong']);
        exit;
    }
    
    // Check if review exists
    $check = mysqli_query($conn, "SELECT id FROM paket_reviews WHERE id = '$review_id'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Review tidak ditemukan']);
        exit;
    }
    
    // Check if admin already replied
    $check_reply = mysqli_query($conn, "SELECT id FROM review_replies WHERE review_id = '$review_id'");
    
    if (mysqli_num_rows($check_reply) > 0) {
        // Update existing reply
        $query = "UPDATE review_replies SET reply_text = '$reply_text', updated_at = NOW() 
                  WHERE review_id = '$review_id'";
    } else {
        // Insert new reply
        $query = "INSERT INTO review_replies (review_id, admin_id, reply_text, created_at) 
                  VALUES ('$review_id', '$admin_id', '$reply_text', NOW())";
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Reply berhasil dikirim!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengirim reply']);
    }
}

// ================= GET ADMIN REPLY =================
elseif ($action == 'get_reply') {
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    
    $query = "SELECT rr.*, 'Admin JawaTrip' as admin_name 
              FROM review_replies rr 
              WHERE rr.review_id = '$review_id' 
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $reply = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'reply' => $reply]);
    } else {
        echo json_encode(['success' => true, 'reply' => null]);
    }
}
?>
