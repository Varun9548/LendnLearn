<?php
ob_start();
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['userid'] === '') {
    header("Location:../sessionExpire.php");
    exit;
}
require_once("dbconnect.php");
?>
