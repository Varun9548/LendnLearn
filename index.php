<?php
session_start();

if (!empty($_SESSION['userid'])) {
    if (!empty($_SESSION['utype']) && strtoupper($_SESSION['utype']) === 'ADMIN') {
        header("Location: admin_dashboard.php");
        exit;
    }

    header("Location: home.php");
    exit;
}

header("Location: index2.html");
exit;
?>