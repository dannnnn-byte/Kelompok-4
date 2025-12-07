<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melanjutkan.";
    header("Location: login.php");
    exit();
}
?>
