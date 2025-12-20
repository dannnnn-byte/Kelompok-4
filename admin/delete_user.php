<?php
session_start();
require_once '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($userId < 1) { header('Location: user_list.php'); exit; }

// Optional: prevent deleting own account via this endpoint
if (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $userId) {
    header('Location: user_list.php');
    exit;
}

$stmt = $conn->prepare('DELETE FROM users WHERE id_user = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

header('Location: user_list.php');
exit;
