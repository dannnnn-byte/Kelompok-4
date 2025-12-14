<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function onlyAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }
}
