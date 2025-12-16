<?php
include 'koneksi.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$search = $input['search'] ?? '';
$location = $input['location'] ?? 'all';
$duration = $input['duration'] ?? 'all';
$price = $input['price'] ?? 'all';
$category = $input['category'] ?? 'all';
$sort = $input['sort'] ?? 'default';

// Build query
$query = "SELECT p.*, k.nama_kota 
          FROM paket_wisata p 
          JOIN kota k ON p.id_kota = k.id_kota 
          WHERE 1=1";

// Search filter
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (p.nama_paket LIKE '%$search%' OR k.nama_kota LIKE '%$search%' OR p.deskripsi LIKE '%$search%')";
}

// Location filter
if ($location !== 'all') {
    $location = mysqli_real_escape_string($conn, $location);
    $query .= " AND k.nama_kota LIKE '%$location%'";
}

// Duration filter
if ($duration !== 'all') {
    if ($duration == '1') {
        $query .= " AND p.durasi LIKE '1%'";
    } elseif ($duration == '2') {
        $query .= " AND p.durasi LIKE '2%'";
    } elseif ($duration == '3') {
        $query .= " AND (p.durasi LIKE '3%' OR p.durasi LIKE '4%' OR p.durasi LIKE '5%')";
    }
}

// Price filter
if ($price !== 'all') {
    if ($price == '1') {
        $query .= " AND p.harga_per_pax < 500000";
    } elseif ($price == '2') {
        $query .= " AND p.harga_per_pax BETWEEN 500000 AND 1000000";
    } elseif ($price == '3') {
        $query .= " AND p.harga_per_pax > 1000000";
    }
}

// Category filter (based on keywords in name/description)
if ($category !== 'all') {
    $category = mysqli_real_escape_string($conn, $category);
    $query .= " AND (p.nama_paket LIKE '%$category%' OR p.deskripsi LIKE '%$category%')";
}

// Sorting
if ($sort == 'price_low') {
    $query .= " ORDER BY p.harga_per_pax ASC";
} elseif ($sort == 'price_high') {
    $query .= " ORDER BY p.harga_per_pax DESC";
} elseif ($sort == 'name') {
    $query .= " ORDER BY p.nama_paket ASC";
} else {
    $query .= " ORDER BY p.id_paket DESC";
}

$result = mysqli_query($conn, $query);
$results = [];

while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
}

echo json_encode(['success' => true, 'results' => $results]);
?>
