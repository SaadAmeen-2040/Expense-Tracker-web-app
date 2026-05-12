<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin($requireAdmin = false) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    if ($requireAdmin && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')) {
        header("Location: index.php?error=unauthorized");
        exit;
    }
}

function redirectIfLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    }
}
?>
